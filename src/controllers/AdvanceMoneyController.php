<?php

namespace hesabro\hris\controllers;

use common\models\Account;
use common\models\Document;
use common\models\Operation;
use common\models\AccountDefinite;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\AdvanceMoneyForm;
use hesabro\hris\models\AdvanceMoneySearch;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\RejectForm;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class AdvanceMoneyController extends Controller
{
    use AjaxValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['AdvanceMoney/manage', 'superadmin'],
                            'actions' => ['index', 'confirm', 'reject'],
                        ],
                        [
                            'allow' => true,
                            'roles' => ['advance-money/infinite', 'superadmin'],
                            'actions' => ['create', 'create-with-confirm'],
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AdvanceMoney models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdvanceMoneySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new AdvanceMoney([
            'scenario' => AdvanceMoney::SCENARIO_CREATE_INFINITE,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'msg' => Module::t('module', 'Item Created')
            ];
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AdvanceMoney::SCENARIO_REJECT);
        $form = new RejectForm();
        if (!$model->canReject()) {
            throw new NotFoundHttpException($model->error_msg);
        }
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->reject_comment = $form->description;
                $model->status = AdvanceMoney::STATUS_REJECT;
                $flag = $model->save();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', 'Item Rejected')
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => Html::errorSummary($model)
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $result = [
                    'success' => false,
                    'msg' => $e->getMessage()
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }

        $this->performAjaxValidation($form);
        return $this->renderAjax('_reject', [
            'model' => $form,
        ]);
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws yii\base\ExitException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AdvanceMoney::SCENARIO_CONFIRM);
        $model->m_debtor_id = Module::getInstance()->settings::get('m_debtor_advance_money');
        $model->receipt_date = Yii::$app->jdate->date("Y/m/d");
        if (!$model->canConfirm()) {
            throw new NotFoundHttpException($model->error_msg);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $result = ['success' => false, 'msg' => Yii::t('app', 'Error In Save Info')];
            $transaction = \Yii::$app->db->beginTransaction();
            try {

                $flag = $model->confirm();
                $flag = $flag && $model->saveDocument();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'success' => true,
                        'msg' => Yii::t('app', 'Item Confirmed')
                    ];

                    if ($model->btn_type == 'save') {
                        $result['hideModal'] = true;
                    } else {
                        $result['redirect'] = true;
                        $result['url'] = Url::to(['/accounting/document/view', 'id' => $model->document->id]);
                    }
                } else {
                    $transaction->rollBack();

                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $result = [
                    'success' => false,
                    'msg' => $e->getMessage()
                ];
            }

            return $this->asJson($result);
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_confirm', [
            'model' => $model,
        ]);
    }

    public function actionCreateWithConfirm($user_id)
    {
        $employee = $this->findModelEmployeeUser($user_id);
        $model = new AdvanceMoneyForm([
            'user_id' => $employee->user_id,
            'employee' => $employee,
            'account_id_to' => $employee->account_id,
        ]);

        if (!$model->canCreate()) {
            throw new ForbiddenHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->saveDocument();
                    $flag = $flag && $model->saveAdvanceMoney();
                    if ($flag) {
                        $result = [
                            'success' => true,
                            'msg' => Module::t('module', "Item Created")
                        ];
                        if(Yii::$app->request->post('TypeBtn', null) == 'document'){
                            $result['CallFunction'] = 'showModalAfterAjax';
                            $result['ModalUrl'] = Url::to(['/document/view', 'id' => $model->id]);
                        }
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                }
                return $this->asJson($result);
            }
        } else {
            $model->loadDefaultValues();
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_create-with-confirm', [
            'model' => $model,
            'accountDefiniteFind' => Url::to(['/account-definite/find', 'level' => AccountDefinite::LEVEL_DEFINITE]),
            'accountList' => Url::to(['/account/get-account'])
        ]);
    }

    /**
     * Finds the AdvanceMoney model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdvanceMoney the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModel($id)
    {
        if (($model = AdvanceMoney::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    private function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }

    /**
     * Finds the EmployeeBranchUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $userId
     * @return EmployeeBranchUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModelEmployeeUser($userId)
    {
        if (($model = EmployeeBranchUser::find()->andWhere(['user_id' => $userId])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
