<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\AdvanceMoneySearch;
use hesabro\hris\models\RejectForm;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AdvanceMoneyController implements the CRUD actions for AdvanceMoney model.
 */
class AdvanceMoneyBase extends Controller
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

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
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
