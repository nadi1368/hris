<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\components\CsvExport;
use common\models\Document;
use common\models\DocumentDetails;
use hesabro\changelog\models\MGLogs;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\SalaryPeriod;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RewardPeriodItemsController extends RewardPeriodItemsBase
{
    public function actionExportExcel($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT);
        $model->load(Yii::$app->request->post());
        $rows = [];
        $rows[0] = [];
        $totalCount = 0;
        $totalBalance = 0;;
        $document = Document::find()->findByModel($model->id)->findByType(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD_PAYMENT)->one();
        foreach ($document->getDocumentDetails()->all() as $index => $item) {
            /** @var DocumentDetails $item */
            if ($item->debtor > 0) {
                if (($employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $item->account->aCustomer->user->id])->one()) !== null) {
                    $salary_item = $model->getSalaryPeriodItems()->andWhere(['user_id' => $item->account->aCustomer->user->id, 'can_payment' => Yii::$app->helper::CHECKED])->limit(1)->one();
                    if ($salary_item !== null) {
                        $rows[$index + 1] = [
                            'IR' . $employeeUser->shaba,
                            '',
                            $item->debtor,
                            $employeeUser->user->fullName,
                            "پرداختی حقوق " . $model->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $model->start_date),
                        ];
                        $totalBalance += $item->debtor;
                        $totalCount++;
                    }
                } else {
                    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.=>' . $item->id));
                }

            }
        }

        $fileId = Yii::$app->jdf->jdate("ymd") . '00' . $model->file_number;
        $fileName = 'IR' . $model->shaba . $fileId . '.txt';
        $rows[0] = [
            'IR' . $model->shaba,
            Yii::$app->jdf->jdate("Ymd"),
            $fileId,
            $totalCount,
            $totalBalance,
            $model->bank_name
        ];
        $csvExport = new CsvExport();
        $csvExport->array_to_csv_download($rows, $fileName);
    }

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionExcelBankWithNative($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            $rows = [];
            $rows[0] = [];
            $IBANList = [];
            $totalCount = 0;
            $totalBalance = 0;

            $index = 1;
            $document = Document::find()->findByModel($model->id)->findByType(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD_PAYMENT)->one();
            foreach ($document->getDocumentDetails()->all() as $item) {
                /** @var DocumentDetails $item */
                if ($item->debtor > 0) {
                    if (($employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $item->account->aCustomer->user->id])->one()) !== null) {
                        $salary_item = $model->getSalaryPeriodItems()->andWhere(['user_id' => $item->account->aCustomer->user->id, 'can_payment' => Yii::$app->helper::CHECKED])->limit(1)->one();
                        if ($salary_item !== null) {
                            if (!$employeeUser->canPaymentSalary()) {
                                throw new HttpException(400, $employeeUser->error_msg);
                            }
                            if (in_array($employeeUser->shaba, $IBANList)) {
                                throw new HttpException(400, $employeeUser->user->fullName . ' : شماره شبا این کارمند تکراری است');
                            }
                            $IBANList[] = $employeeUser->shaba;
                            $rows[$index++] = [
                                'IR' . $employeeUser->shaba,
                                '',
                                $item->debtor,
                                $employeeUser->user->fullName,
                                "پرداختی حقوق " . $model->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $model->start_date),
                            ];
                            $totalBalance += $item->debtor;
                            $totalCount++;
                        }
                    } else {
                        throw new HttpException(400, $item->account->aCustomer->fullName . ' در مشخصات کارمندی ثبت نشده است.');
                    }

                }
            }

            foreach (is_array($model->another_period) ? $model->another_period : [] as $anotherPeriodId)
            {
                $anotherPeriodModel = $this->findModelPeriod($anotherPeriodId);
                $document = Document::find()->findByModel($anotherPeriodModel->id)->findByType(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD_PAYMENT)->one();
                foreach ($document->getDocumentDetails()->all() as $item) {
                    /** @var DocumentDetails $item */
                    if ($item->debtor > 0) {
                        if (($employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $item->account->aCustomer->user->id])->one()) !== null) {
                            $salary_item = $anotherPeriodModel->getSalaryPeriodItems()->andWhere(['user_id' => $item->account->aCustomer->user->id, 'can_payment' => Yii::$app->helper::CHECKED])->limit(1)->one();
                            if ($salary_item !== null) {
                                if (!$employeeUser->canPaymentSalary()) {
                                    throw new HttpException(400, $employeeUser->error_msg);
                                }
                                if (in_array($employeeUser->shaba, $IBANList)) {
                                    throw new HttpException(400, $employeeUser->user->fullName . ' : شماره شبا این کارمند تکراری است');
                                }
                                $IBANList[] = $employeeUser->shaba;
                                $rows[$index++] = [
                                    'IR' . $employeeUser->shaba,
                                    '',
                                    $item->debtor,
                                    $employeeUser->user->fullName,
                                    "پرداختی حقوق " . $anotherPeriodModel->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $anotherPeriodModel->start_date),
                                ];
                                $totalBalance += $item->debtor;
                                $totalCount++;
                            }
                        } else {
                            throw new HttpException(400, $item->account->aCustomer->fullName . ' در مشخصات کارمندی ثبت نشده است.');
                        }

                    }
                }
            }

            $fileId = Yii::$app->jdf->jdate("ymd") . '00' . $model->file_number;
            $fileName = 'IR' . $model->shaba . $fileId . '.txt';
            $rows[0] = [
                'IR' . $model->shaba,
                Yii::$app->jdf->jdate("Ymd"),
                $fileId,
                $totalCount,
                $totalBalance,
                $model->bank_name
            ];
            MGLogs::saveManual(SalaryPeriod::class, $model->id, $rows);
            return [
                'success' => true,
                'msg' => Yii::t("app", "Item Created"),
                'html' => $this->renderAjax('_excel-bank-send-to-native', [
                    'model' => $model,
                    'rows' => json_encode($rows),
                    'fileName' => $fileName,
                ]),
            ];

        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form-bank-with-native', [
            'model' => $model,
        ]);
    }
}
