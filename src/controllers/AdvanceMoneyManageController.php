<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\AdvanceMoneySearch;
use hesabro\hris\models\RejectForm;
use hesabro\hris\models\AdvanceMoneyForm;
use hesabro\hris\models\EmployeeBranchUser;
use common\models\Document;
use common\models\Operation;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AdvanceMoneyController implements the CRUD actions for AdvanceMoney model.
 */
class AdvanceMoneyManageController extends Controller
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
                            'roles' => ['AdvanceMoney/manage'],
                            'actions' => ['index', 'confirm', 'reject'],
                        ],
                        [
                            'allow' => true,
                            'roles' => ['advance-money/infinite'],
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
                'msg' => Yii::t('app', 'Item Created')
            ];
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AdvanceMoney::SCENARIO_CONFIRM);
        if (!$model->canConfirm()) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $operation = new Operation();
        $operation->setScenario(Operation::SCENARIO_PAYMENT_TO_CUSTOMER);
        $operation->price = (int)$model->amount;
        if ($operation->load(Yii::$app->request->post()) && $operation->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['success' => false, 'msg' => Yii::t('app', 'Error In Save Info')];

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $operation->saveDocument(Document::TYPE_PAYMENT_TO_CUSTOMER, $operation->serial);
                $model->doc_id = $operation->document->id;
                $model->status = AdvanceMoney::STATUS_CONFIRM;
                $flag = $flag && $model->save();

                if ($flag) {
                    $transaction->commit();
                    $response = [
                        'success' => true,
                        'msg' => Yii::t('app', 'Item Confirmed')
                    ];
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $response = [
                    'success' => false,
                    'msg' => $e->getMessage()
                ];
            }


            return $response;
        }
        $this->performAjaxValidation($model);
        $operation->date = Yii::$app->jdate->date("Y/m/d");
        $operation->des = $model->comment . " - درخواست مساعده #" . $model->id;
        return $this->renderAjax('_confirm', [
            'model' => $model,
            'operation' => $operation,
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
                        'msg' => Yii::t("app", 'Item Rejected')
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

    public function actionCreateWithConfirm($user_id)
    {
        $employee = $this->findModelEmployeeUser($user_id);
        $model = new AdvanceMoneyForm([
            'user_id' => $employee->user_id,
            'employee' => $employee,
            'account_id_to' => $employee->account_id,
        ]);

        if (!$model->canCreate()) {
            throw new ForbiddenHttpException($model->error_msg ?: Yii::t("app", "It is not possible to perform this operation"));
        }
        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
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
                            'msg' => Yii::t("app", "Item Created")
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
        ]);
    }

    /**
     * Finds the AdvanceMoney model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdvanceMoney the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdvanceMoney::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
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
    protected function findModelEmployeeUser($userId)
    {
        if (($model = EmployeeBranchUser::find()->andWhere(['user_id' => $userId])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
