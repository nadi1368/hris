<?php

namespace hesabro\hris\controllers;

use backend\models\RejectForm;
use backend\models\UploadExcelSearch;
use hesabro\hris\models\EmployeeBranchUser;
use common\components\jdf\Jdf;
use common\models\UploadExcel;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\hris\models\SalaryItemsAddition;
use hesabro\hris\models\SalaryItemsAdditionSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use function GuzzleHttp\Psr7\str;

/**
 * SalaryItemsAdditionController implements the CRUD actions for SalaryItemsAddition model.
 */
class SalaryItemsAdditionController extends Controller
{
    use AjaxValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['SalaryPeriod/index'],
                            'actions' => ['index', 'create', 'update', 'delete', 'view', 'report-leave', 'chart-leave', 'list-csv-salary-non-cash', 'upload-salary-non-cash', 'insert-salary-non-cash']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['SalaryPeriod/confirm'],
                            'actions' => ['confirm', 'confirm-selected', 'return-status', 'reject']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all SalaryItemsAddition models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalaryItemsAdditionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all SalaryItemsAddition models.
     * @return mixed
     */
    public function actionReportLeave()
    {
        $searchModel = new SalaryItemsAdditionSearch();
        $dataProvider = $searchModel->searchReportLeave(Yii::$app->request->queryParams);

        return $this->render('report-leave', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionChartLeave()
    {
        $searchModel = new SalaryItemsAdditionSearch();
        $chartData = $searchModel->searchReportLeaveLineChart(Yii::$app->request->queryParams);

        return $this->render('chart-leave', [
            'searchModel' => $searchModel,
            'chartData' => $chartData
        ]);
    }

    /**
     * Displays a single SalaryItemsAddition model.
     * @param int $id شناسه
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
     * @param $kind
     * @return array|string
     * @throws Yii\base\ExitException
     */
    public function actionCreate($kind)
    {
        $model = new SalaryItemsAddition(['kind' => $kind]);
        $model->setDefaultValueBeforeCreate();
        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Yii::t("app", "Item Created")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
        $this->performAjaxValidation($model);
        switch ($model->kind) {
            case SalaryItemsAddition::KIND_LOW_TIME:
                return $this->renderAjax('_form-low-time', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_OVER_TIME:
                return $this->renderAjax('_form-over-time', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_LEAVE_HOURLY:
                return $this->renderAjax('_form-leave-hourly', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_LEAVE_DAILY:
                return $this->renderAjax('_form-leave-daily', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_COMMISSION:
                return $this->renderAjax('_form-commission', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_COMMISSION_CONST:
                return $this->renderAjax('_form-commission-const', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_NON_CASH:
                return $this->renderAjax('_form-non-cash', [
                    'model' => $model,
                ]);
        }
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            throw new NotFoundHttpException($model->error_msg);
        }
        $model->setDefaultValueBeforeUpdate();
        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];
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
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
        $this->performAjaxValidation($model);
        switch ($model->kind) {
            case SalaryItemsAddition::KIND_LOW_TIME:
                return $this->renderAjax('_form-low-time', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_OVER_TIME:
                return $this->renderAjax('_form-over-time', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_LEAVE_HOURLY:
                return $this->renderAjax('_form-leave-hourly', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_LEAVE_DAILY:
                return $this->renderAjax('_form-leave-daily', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_COMMISSION:
                return $this->renderAjax('_form-commission', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_COMMISSION_CONST:
                return $this->renderAjax('_form-commission-const', [
                    'model' => $model,
                ]);
            case SalaryItemsAddition::KIND_NON_CASH:
                return $this->renderAjax('_form-non-cash', [
                    'model' => $model,
                ]);
        }
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete() && $model->softDelete()) {
            $result = [
                'status' => true,
                'message' => Yii::t("app", "Item Deleted")
            ];
        } else {
            $result = [
                'status' => false,
                'message' => Yii::t("app", "Error In Save Info")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * @param $id
     * @return false|string
     * @throws NotFoundHttpException
     */
    public function actionConfirm($id)
    {
        $response = ['success' => false, 'data' => '', 'msg' => Yii::t('app', 'Error In Save Info')];
        $model = $this->findModel($id);
        if ($model->canConfirm()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->confirm();
                if ($flag) {
                    $transaction->commit();
                    $response['success'] = true;
                    $response['msg'] = Yii::t("app", "Item Confirmed");

                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        } else {
            $response['msg'] = Yii::t('app', 'It is not possible to perform this operation');
        }
        return json_encode($response);
    }

    /**
     * @param $id
     * @return false|string
     * @throws NotFoundHttpException
     */
    public function actionReturnStatus($id)
    {
        $response = ['success' => false, 'data' => '', 'msg' => Yii::t('app', 'Error In Save Info')];
        $model = $this->findModel($id);
        if ($model->canReturnStatus()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->returnStatus();
                if ($flag) {
                    $transaction->commit();
                    $response['success'] = true;
                    $response['msg'] = Yii::t("app", "Item Updated");

                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        } else {
            $response['msg'] = Yii::t('app', 'It is not possible to perform this operation');
        }
        return json_encode($response);
    }

    /**
     * @param String $selectedIds
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConfirmSelected(string $selectedIds): Response
    {
        $countConfirm = 0;
        foreach (explode(',', $selectedIds) as $selectedId) {
            $selectedId = @unserialize($selectedId) === false ? $selectedId : ((string)unserialize($selectedId));
            $model = $this->findModel($selectedId);
            if ($model->canConfirm()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->confirm();
                    if ($flag) {
                        $transaction->commit();
                        $countConfirm++;
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                    return $this->asJson([
                        'status' => false,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $this->asJson([
            'status' => true,
            'message' => $countConfirm > 0 ? $countConfirm . " سطر تایید شد." : "هیچ سطری تایید نشد",
        ]);
    }

    /**
     * @param $id
     * @param $status
     * @return array|string
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $form = new RejectForm();
        if ($model->canReject()) {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->reject($form->description);
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
                    $result = [
                        'success' => false,
                        'msg' => $e->getMessage()
                    ];
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $result;
            }
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $this->performAjaxValidation($form);

        return $this->renderAjax('_form_reject', [
            'model' => $form,
        ]);
    }


    /**
     * @return string
     */
    public function actionListCsvSalaryNonCash()
    {
        $searchModel = new UploadExcelSearch(['type' => [UploadExcel::TYPE_SALARY_NON_CASH]]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list-csv-salary-non-cash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUploadSalaryNonCash()
    {
        $uploadForm = new UploadExcel(['scenario' => UploadExcel::SCENARIO_UPLOAD_SALARY_NON_CASH, 'type' => UploadExcel::TYPE_SALARY_NON_CASH, 'month' => Yii::$app->jdate->date("m", strtotime('-10 DAY'))]);
        if ($uploadForm->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $uploadForm->date = Yii::$app->jdate->date("Y/") . $uploadForm->month . '/01';
                if ($uploadForm->save()) {
                    $transaction->commit();
                    $this->flash('success', 'آپلود با موفقیت انجام شد.');
                    return $this->redirect(['insert-salary-non-cash', 'csv_id' => $uploadForm->id]);

                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Yii::t("app", "Error In Save Info"));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage() . $e->getTraceAsString());
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        }
        return $this->render('upload-salary-non-cash', [
            'uploadForm' => $uploadForm,
        ]);
    }


    /**
     * @param $csv_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionInsertSalaryNonCash($csv_id)
    {
        $excelFile = $this->findModelUploadFile($csv_id);
        if ($excelFile->status != UploadExcel::STATUS_ACTIVE) {
            throw new NotFoundHttpException('فایل در تاریخ مورد نظر ثبت شده است.اگر اشتباه ثبت شده.لطفا ابتدا فایل رو پاک نمایید.');
        }
        $file = $excelFile->getStorageFile('excelFile')->one();
        $fileContent = $file->getFileContent();
        $tmpFileName = tempnam(sys_get_temp_dir(), '');
        file_put_contents(
            $tmpFileName,
            $fileContent
        );
        if (!file_exists($tmpFileName)) {
            throw new NotFoundHttpException('فایل مورد نظر یافت نشد.');
        }
        $dataInsert = 0;
        $error = '';
        $flag = true;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (($handle = fopen($tmpFileName, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                    $data = array_reverse($data);
                    $employeeNationalCode = (string)$data[2];
                    if ($employeeNationalCode > 0 && ($employee = EmployeeBranchUser::find()->byNationalCode($employeeNationalCode)->limit(1)->one()) !== null) {
                        if (($debtor = (int)$data[0]) > 0) {
                            $dataInsert++;
                            $model = new SalaryItemsAddition([
                                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                                'user_id' => $employee->user_id,
                                'kind' => SalaryItemsAddition::KIND_NON_CASH,
                                'type' => SalaryItemsAddition::TYPE_PAY_BUY,
                                'from_date' => $excelFile->date,
                                'second' => $debtor,
                                'description' => Html::encode($data[6]),
                                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                                'period_id' => $excelFile->id
                            ]);
                            $flag = $flag && $model->save();
                        }
                    }

                }
                fclose($handle);
            }
            $flag = $flag && $excelFile->setInserted();
            if ($flag) {
                $transaction->commit();
                if ($dataInsert > 0) {
                    $this->flash("info", "مشخصات  $dataInsert کارمند با موفقت اضافه شد.");
                } else {
                    $this->flash("warning", 'هیج کارمندی یافت نشد');
                }

            } else {
                $transaction->rollBack();
                $this->flash("warning", !empty($error) ? $error : Yii::t("app", "Error In Save Info"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->flash('warning', $e->getMessage());
            Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        }
        return $this->redirect(['index']);
    }


    /**
     * Finds the SalaryItemsAddition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return SalaryItemsAddition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalaryItemsAddition::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    /**
     * Finds the UploadExcel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UploadExcel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelUploadFile(int $id)
    {
        if (($model = UploadExcel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
