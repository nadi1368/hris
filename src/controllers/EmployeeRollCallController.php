<?php

namespace hesabro\hris\controllers;

use backend\models\UploadExcelSearch;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeRollCall;
use hesabro\hris\models\EmployeeRollCallSearch;
use hesabro\hris\models\SalaryPeriod;
use backend\modules\excel\models\UploadFormExcel;
use hesabro\helpers\components\Helper;
use hesabro\helpers\components\Jdf;
use common\models\UploadExcel;
use hesabro\helpers\validators\PersianValidator;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * EmployeeRollCallController implements the CRUD actions for EmployeeRollCall model.
 */
class EmployeeRollCallController extends Controller
{
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
                            'roles' => ['EmployeeBranch/RollCall'],
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all EmployeeRollCall models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeRollCallSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     */
    public function actionListCsv()
    {
        $searchModel = new UploadExcelSearch(['type' => [UploadExcel::TYPE_ROLL_CALL_DAILY]]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list-csv', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUploadCsvMonthly(int $period_id)
    {
        $salaryPeriod = $this->findModelPeriod($period_id);
        $uploadForm = new UploadExcel(['scenario' => UploadExcel::SCENARIO_UPLOAD_RollCall_DAILY, 'type' => UploadExcel::TYPE_ROLL_CALL_DAILY, 'modelId' => $salaryPeriod->id]);
        if ($uploadForm->load(Yii::$app->request->post()) && $uploadForm->save()) {
            $this->flash('success', 'آپلود با موفقیت انجام شد.');
            return $this->redirect(['csv', 'period_id' => $salaryPeriod->id, 'csv_id' => $uploadForm->id]);
        }

        return $this->render('upload-csv-monthly', [
            'uploadForm' => $uploadForm,
            'model' => $salaryPeriod,
            'salaryPeriod' => $salaryPeriod,
        ]);
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUploadCsvDaily()
    {
        $uploadForm = new UploadExcel(['scenario' => UploadExcel::SCENARIO_UPLOAD_RollCall_DAILY, 'type' => UploadExcel::TYPE_ROLL_CALL_DAILY, 'date' => Jdf::plusDay(Yii::$app->jdf->jdate("Y/m/d"), -1)]);
        if ($uploadForm->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($uploadForm->save()) {
                    $transaction->commit();
                    $this->flash('success', 'آپلود با موفقیت انجام شد.');
                    return $this->redirect(['insert-csv-daily', 'csv_id' => $uploadForm->id]);

                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Yii::t("app", "Error In Save Info"));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage());
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        }
        return $this->render('upload-csv-daily', [
            'uploadForm' => $uploadForm,
        ]);
    }

    /**
     * @param $csv_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionInsertCsvDaily($csv_id)
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
                    $rollCallId = (int)$data[0];
                    if ($rollCallId>0 && ($employee = EmployeeBranchUser::find()->byRollCall($rollCallId)->limit(1)->one()) !== null) {
                        $dataInsert++;
                        $model = new EmployeeRollCall([
                            'scenario' => EmployeeRollCall::SCENARIO_INSERT,
                            'user_id' => $employee->user_id,
                            'employee' => $employee,
                            't_id' => $rollCallId,
                            'date' => $excelFile->date,
                            'status' => PersianValidator::replaceChar($data[2]),
                            'total' => \Yii::$app->phpNewVer->strReplace('#', '', $data[3]),
                            'shift' => \Yii::$app->phpNewVer->strReplace('#', '', $data[4]),
                            'over_time' => EmployeeRollCall::convertToMinutes($data[5]),
                            'low_time' => EmployeeRollCall::convertToMinutes($data[6]),
                            'leave_time' => EmployeeRollCall::convertToMinutes($data[16]),
                            'mission_time' => EmployeeRollCall::convertToMinutes($data[15]),
                            'in_1' => \Yii::$app->phpNewVer->strReplace('#', '', $data[7]),
                            'out_1' => \Yii::$app->phpNewVer->strReplace('#', '', $data[8]),
                            'in_2' => \Yii::$app->phpNewVer->strReplace('#', '', $data[9]),
                            'out_2' => \Yii::$app->phpNewVer->strReplace('#', '', $data[10]),
                            'in_3' => \Yii::$app->phpNewVer->strReplace('#', '', $data[11]),
                            'out_3' => \Yii::$app->phpNewVer->strReplace('#', '', $data[12]),
                            'period_id' => $excelFile->id
                        ]);
                        $flag = $flag && $model->save();
                        if (!$flag) {
                            $error = "خطا در ذخیره اطلاعات حضور و غیاب کارمند " . $employee->user->fullName;
                            $error .= Html::errorSummary($model, ['header' => '']);
                            break;
                        }
                        $model->refresh();
                        $flag = $flag && $model->saveLowTime();
                        $flag = $flag && $model->saveOverTime();
                        $flag = $flag && $model->saveLeaveTime();
                        $flag = $flag && $model->saveLeaveDay();
                        $flag = $flag && $model->saveAbsentDay();
                    }
                }
                fclose($handle);
            }
            $flag = $flag && $excelFile->setInserted();
            if ($flag) {
                $transaction->commit();
                if ($dataInsert > 0) {
                    $this->flash("success", "مشخصات  $dataInsert کارمند با موفقت اضافه شد.");
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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeleteCsvDaily($id): array
    {
        $model = $this->findModelUploadFile($id);
        $result = [
            'status' => false,
            'message' => Yii::t("app", "Error In Save Info")
        ];
        if ($model->canDelete()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->softDelete();
                $flag = $flag && $model->afterSoftDelete();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Yii::t("app", "Item Deleted")
                    ];
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * /**
     * @param $period_id
     * @param $file_name
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionInsertCsvMonthly(int $period_id, string $file_name)
    {
        $salaryPeriod = $this->findModelPeriod($period_id);
        $fileName = UploadFormExcel::getUploadPath() . $file_name;
        if (!file_exists($fileName)) {
            throw new NotFoundHttpException('فایل مورد نظر یافت نشد.' . $fileName);
        }
        $row = 1;
        $rollCallId = 1;
        $employee = EmployeeBranchUser::find()->byRollCall($rollCallId)->limit(1)->one();
        $overTime = 0;
        $lowTime = 0;
        $oldRollCallId = 1;
        $flag = true;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (($handle = fopen($fileName, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                    $data = array_reverse($data);
                    $row++;
                    if ($data[0] < $oldRollCallId) {
                        if ($employee !== null) {
                            $flag = $flag && $employee->saveLowTime(Yii::$app->jdf->jdate("Y/m/d", $salaryPeriod->start_date), $lowTime, $salaryPeriod->id);
                            $flag = $flag && $employee->saveOverTime(Yii::$app->jdf->jdate("Y/m/d", $salaryPeriod->start_date), $overTime, $salaryPeriod->id);
                        }
                        $lowTime = 0;
                        $overTime = 0;
                        $rollCallId++;
                        $employee = EmployeeBranchUser::find()->byRollCall($rollCallId)->limit(1)->one();
                    }
                    if ($employee !== null) {
                        $model = new EmployeeRollCall([
                            'scenario' => EmployeeRollCall::SCENARIO_INSERT,
                            'user_id' => $employee->user_id,
                            'employee' => $employee,
                            't_id' => $rollCallId,
                            'date' => $data[0] > 9 ? Yii::$app->jdf->jdate("Y/m/$data[0]", $salaryPeriod->start_date) : Yii::$app->jdf->jdate("Y/m/0$data[0]", $salaryPeriod->start_date),
                            'status' => $data[2],
                            'total' => \Yii::$app->phpNewVer->strReplace('#', '', $data[3]),
                            'shift' => \Yii::$app->phpNewVer->strReplace('#', '', $data[4]),
                            'over_time' => EmployeeRollCall::convertToMinutes($data[5]),
                            'low_time' => EmployeeRollCall::convertToMinutes($data[6]),
                            'leave_time' => EmployeeRollCall::convertToMinutes($data[7]),
                            'mission_time' => EmployeeRollCall::convertToMinutes($data[8]),
                            'in_1' => \Yii::$app->phpNewVer->strReplace('#', '', $data[9]),
                            'out_1' => \Yii::$app->phpNewVer->strReplace('#', '', $data[10]),
                            'in_2' => \Yii::$app->phpNewVer->strReplace('#', '', $data[11]),
                            'out_2' => \Yii::$app->phpNewVer->strReplace('#', '', $data[12]),
                            'in_3' => \Yii::$app->phpNewVer->strReplace('#', '', $data[13]),
                            'out_3' => \Yii::$app->phpNewVer->strReplace('#', '', $data[14]),
                            'period_id' => $salaryPeriod->id
                        ]);
                        $flag = $flag && $model->save();
                        $model->refresh();
                        $lowTime += $model->low_time;
                        $overTime += $model->over_time;
                        $flag = $flag && $model->saveOverTime(); // فقط تعطیل کاری
                        $flag = $flag && $model->saveLeaveTime();
                        $flag = $flag && $model->saveLeaveDay();
                        $flag = $flag && $model->saveAbsentDay();
                    }
                    $oldRollCallId = $data[0];
                }
                fclose($handle);
            }
            if ($employee !== null) {
                $flag = $flag && $employee->saveLowTime(Yii::$app->jdf->jdate("Y/m/d", $salaryPeriod->start_date), $lowTime, $salaryPeriod->id);
                $flag = $flag && $employee->saveOverTime(Yii::$app->jdf->jdate("Y/m/d", $salaryPeriod->start_date), $overTime, $salaryPeriod->id);
            }
            $salaryPeriod->setRollCall = Helper::YES;
            $flag = $flag && $salaryPeriod->save(false);
            if ($flag) {
                $transaction->commit();
                $this->flash("success", Yii::t('app', 'Item Created'));

            } else {
                $transaction->rollBack();
                $this->flash("warning", Yii::t("app", "Error In Save Info"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->flash('warning', $e->getMessage());
            Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        }
        return $this->redirect(['index']);
    }


    /**
     * Finds the EmployeeRollCall model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return EmployeeRollCall the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeeRollCall::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the SalaryPeriod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return SalaryPeriod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelPeriod(int $id)
    {
        if (($model = SalaryPeriod::findOne($id)) !== null) {
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
