<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\WorkshopInsurance;
use hesabro\helpers\components\Jdf;
use hesabro\hris\Module;
use common\models\Year;
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
class SalaryPeriodController extends Controller
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
     * @return array|string
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
     * @return array|string
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
     * @param $workshop_id
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateReward($workshop_id)
    {
        $model = $this->findModelWorkShop($workshop_id);

        if (!$model->canCreateReward()) {
            throw new HttpException(400, Yii::t("app", "It is not possible to perform this operation"));
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $startAndEndOfCurrentYear = Jdf::getStartAndEndOfCurrentYear(Year::getDefault('endTime'));
            $salaryPeriod = new SalaryPeriod([
                'scenario' => SalaryPeriod::SCENARIO_CREATE_REWARD,
                'workshop_id' => $model->id,
                'title' => 'عیدی و پاداش ' . Yii::$app->jdate->date("Y", $startAndEndOfCurrentYear['start']),
                'kind' => SalaryPeriod::KIND_REWARD,
                'start_date' => $startAndEndOfCurrentYear['start'],
                'end_date' => $startAndEndOfCurrentYear['end'],
            ]);
            $flag = $salaryPeriod->save();
            if ($flag) {
                $transaction->commit();
                $this->flash("success", Yii::t('app', 'Item Created'));
                return $this->redirect(['reward-period-items/index', 'id' => $salaryPeriod->id]);

            } else {
                $transaction->rollBack();
                $this->flash("warning", Yii::t("app", "Error In Save Info"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            $this->flash('warning', $e->getMessage());
        }
        return $this->redirect(['salary-period/index', 'SalaryPeriodSearch[workshop_id]' => $workshop_id]);
    }

    /**
     * @param $workshop_id
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateYear($workshop_id)
    {
        $model = $this->findModelWorkShop($workshop_id);
        if (!$model->canCreateYear()) {
            throw new HttpException(400, Yii::t("app", "It is not possible to perform this operation"));
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $salaryPeriod = new SalaryPeriod([
                'scenario' => SalaryPeriod::SCENARIO_CREATE_YEAR,
                'workshop_id' => $model->id,
                'title' => 'سنوات ' . Year::getDefault('title'),
                'kind' => SalaryPeriod::KIND_YEAR,
                'start_date' => Year::getDefault('start'),
                'end_date' => Year::getDefault('end'),
            ]);
            $flag = $salaryPeriod->save();
            if ($flag) {
                $transaction->commit();
                $this->flash("success", Yii::t('app', 'Item Created'));
                return $this->redirect(['year-period-items/index', 'id' => $salaryPeriod->id]);

            } else {
                $transaction->rollBack();
                $this->flash("warning", Yii::t("app", "Error In Save Info"));
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            $transaction->rollBack();
            $this->flash('warning', $e->getMessage());
        }
        return $this->redirect(['salary-period/index', 'SalaryPeriodSearch[workshop_id]' => $workshop_id]);
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
