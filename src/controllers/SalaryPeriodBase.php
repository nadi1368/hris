<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\WorkshopInsurance;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * SalaryPeriodController implements the CRUD actions for SalaryPeriod model.
 */
class SalaryPeriodBase extends Controller
{
    use AjaxValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['SalaryPeriod/index'],
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all SalaryPeriod models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalaryPeriodSearch();
        $searchModel->workshop_id = WorkshopInsurance::getDefault();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalaryPeriod model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $workshop_id
     * @return array|string|Response
     * @throws \yii\base\ExitException
     */
    public function actionCreate($workshop_id)
    {
        $model = new SalaryPeriod(['scenario' => SalaryPeriod::SCENARIO_CREATE, 'workshop_id' => $workshop_id]);

        $model->loadDefaultValuesBeforeCreate();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->setEndDate();
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Yii::t("app", "Item Created")
                    ];
                    $transaction->commit();
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
                    'msg' => $e->getMessage(),
                ];
            }
            return $this->asJson($result);
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws yii\base\ExitException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(SalaryPeriod::SCENARIO_UPDATE);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Yii::t("app", "Item Updated")
                    ];
                    $transaction->commit();
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
                    'msg' => $e->getMessage(),
                ];
            }
            return $this->asJson($result);
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($model->canDelete() && $model->delete()) {
                $transaction->commit();
                $result = [
                    'status' => true,
                    'message' => Yii::t("app", "Item Deleted")
                ];
            } else {
                $transaction->rollBack();
                $result = [
                    'status' => false,
                    'message' => Yii::t("app", "Error In Save Info")
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $result = [
                'status' => false,
                'message' => $e->getMessage()
            ];
            Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        }
        if (Yii::$app->request->isAjax) {
            return $this->asJson($result);
        } else {
            $this->flash($result['status'] ? 'success' : 'danger', $result['message']);
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the SalaryPeriod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalaryPeriod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalaryPeriod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the WorkshopInsurance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WorkshopInsurance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelWorkShop($id)
    {
        if (($model = WorkshopInsurance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
