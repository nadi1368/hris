<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeBranchUserSearch;
use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\models\SalaryPeriodItemsSearch;
use common\components\CsvExport;
use common\components\Helper;
use common\models\Document;
use common\models\DocumentDetails;
use common\models\mongo\MGLogs;
use common\models\Process;
use common\models\Settings;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * YearPeriodItemsController implements the CRUD actions for SalaryPeriodItems model.
 */
class YearPeriodItemsController extends Controller
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
                        ],
                    ]
            ]
        ];
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($id)
    {
        $salaryPeriod = $this->findModelPeriod($id);
        $searchModel = new SalaryPeriodItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $salaryPeriod->id);

        $userIds = SalaryPeriodItems::find()->select(['user_id'])->andWhere(['period_id'=>$salaryPeriod->id]);
        $searchModelUser = new EmployeeBranchUserSearch();
        $dataProviderUser = $searchModelUser->searchReward(Yii::$app->request->queryParams, $userIds);

        return $this->render('index', [
            'salaryPeriod' => $salaryPeriod,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModelUser' => $searchModelUser,
            'dataProviderUser' => $dataProviderUser,
        ]);
    }

    /**
     * Displays a single SalaryPeriodItems model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single SalaryPeriodItems model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPrint($id)
    {
        $this->layout = '@app/views/layouts/print-factor';
        $salaryPeriod = $this->findModelPeriod($id);
        $items = $salaryPeriod->getSalaryPeriodItems()->all();
        return $this->render('print', [
            'salaryPeriod' => $salaryPeriod,
            'items' => $items,
        ]);
    }

    /**
     * @param $period_id
     * @param $user_id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionCreate($period_id, $user_id)
    {
        $salaryPeriod = $this->findModelPeriod($period_id);

        $employee = $this->findModelEmployee($user_id);
        if (!$salaryPeriod->canCreateItems() || !$employee->canCreateYearPayment()) {
            throw new HttpException(400, Yii::t("app", "It is not possible to perform this operation"));
        }
        $model = new SalaryPeriodItems([
            'scenario' => SalaryPeriodItems::SCENARIO_CREATE_YEAR,
            'period_id' => $salaryPeriod->id,
            'user_id' => $employee->user_id,
        ]);

        $model->loadDefaultValuesBeforeCreateYear($salaryPeriod->end_date, $model->year);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save();
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
                        'msg' => Yii::t("app", "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'success' => false,
                    'msg' => $e->getMessage(),
                ];
            }
            return $result;
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'salaryPeriod' => $salaryPeriod,
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCreateAuto($id)
    {
        $period = $this->findModelPeriod($id);
        $items = SalaryPeriodItems::find()
            ->byYear($period->start_date, $period->end_date)
            ->bySalary()
            ->andWhere(['NOT IN', 'user_id', $period->getSalaryPeriodItems()->select(['user_id'])])
            ->groupBy(['user_id'])
            ->all();
        try {
            $countCopy = 0;
            $countExist = 0;
            $msg_error = '';
            foreach ($items as $item) {
                /** @var SalaryPeriodItems $item */
                $countExist++;
                if ($period->canCreateItems()) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $model = new SalaryPeriodItems([
                            'scenario' => SalaryPeriodItems::SCENARIO_CREATE_YEAR,
                            'period_id' => $period->id,
                            'user_id' => $item->user_id,
                        ]);
                        $model->loadDefaultValuesBeforeCreateYear($period->end_date, $model->year);
                        if ($model->save()) {
                            $countCopy++;
                        } else {
                            $msg_error .= Html::tag('p', $item->user->fullName);
                            $msg_error .= Html::errorSummary($model, ['header' => '']);
                        }
                        $transaction->commit();
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                        $transaction->rollBack();
                    }
                } else {
                    $msg_error .= Html::tag('p', $item->user->fullName);
                }

            }
            if ($countExist == $countCopy) {
                $this->flash('success', "تعداد $countCopy سطر با موفقیت کپی شد.");
            } elseif ($countCopy > 0) {
                $this->flash('info', "از تعداد $countExist سطر $countCopy سطر کپی شد.");
            } else {
                $this->flash('info', 'هیچ سطری کپی نشد.');
            }

            if (!empty($msg_error)) {
                $this->flash('warning', Html::tag('h3', 'سطر های دارای خطا :') . $msg_error);
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            $this->flash('danger', $e->getMessage());
        }

        return $this->redirect(['index', 'id' => $id]);
    }
    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete() && $model->delete()) {
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
     * @return array|string
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            throw new HttpException(400, Yii::t("app", "It is not possible to perform this operation"));
        }
        $salaryPeriod = $model->period;
        $model->setScenario(SalaryPeriodItems::SCENARIO_UPDATE_YEAR);

        $model->loadDefaultValuesBeforeUpdateYear();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save();
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
                        'msg' => Yii::t("app", "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'success' => false,
                    'msg' => $e->getMessage(),
                ];
            }
            return $result;
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'salaryPeriod' => $salaryPeriod,
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array|string
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionUpdateAfterConfirm($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdateAfterConfirm()) {
            throw new HttpException(400, Yii::t("app", "It is not possible to perform this operation"));
        }
        $salaryPeriod = $model->period;
        $model->setScenario(SalaryPeriodItems::SCENARIO_UPDATE_AFTER_CONFIRM);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save();
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
                        'msg' => Yii::t("app", "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'success' => false,
                    'msg' => $e->getMessage(),
                ];
            }
            return $result;
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form-update-after-confirm', [
            'salaryPeriod' => $salaryPeriod,
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * تایید
     */
    public function actionConfirm($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canConfirm()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_CONFIRM;
                $flag = $model->save(false);
                $flag = $flag && $model->saveDocumentInterfaceYear();
                $flag = $flag && $model->saveDocumentConfirmYear();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Yii::t("app", "Item Confirmed")
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
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Yii::t("app", "It is not possible to perform this operation")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionReturnConfirm($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canReturnConfirm()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_WAIT_CONFIRM;
                $flag = $model->save(false);
                $flag = $flag && $model->deleteDocument(Document::TYPE_YEAR_PERIOD_CLEARING);
                $flag = $flag && $model->deleteDocument(Document::TYPE_YEAR_PERIOD);
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Yii::t("app", "Item Confirmed")
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
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Yii::t("app", "It is not possible to perform this operation")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }


    /**
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionPayment($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_PAYMENT);
        $model->payment_date = Yii::$app->jdf->jdate("Y/m/d");
        if (!$model->canPayment()) {
            $this->flash('danger', Yii::t("app", "It is not possible to perform this operation"));
            return $this->redirect(['index', 'id' => $id]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_PAYMENT;
                $flag = $model->save(false);
                $flag = $flag && $model->saveDocumentPaymentYear();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'success' => true,
                        'msg' => Yii::t("app", "Item Created")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => Yii::t("app", "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'success' => false,
                    'msg' => $e->getMessage()
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form-payment', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionReturnPayment($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canReturnPayment()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_CONFIRM;
                $flag = $model->save(false);
                $flag = $flag && $model->deleteDocument(Document::TYPE_SALARY_PERIOD_PAYMENT);
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Yii::t("app", "Item Confirmed")
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
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Yii::t("app", "It is not possible to perform this operation")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionExport($id)
    {
        $model = $this->findModelPeriod($id);
        $itemModel = new SalaryPeriodItems();

        $rows = [];
        $rows[0] = [
            'کد ملی',
            'نام و نام خانوادگی',
            $itemModel->getAttributeLabel('hours_of_work'),
            $itemModel->getAttributeLabel('holiday_of_overtime'),
            $itemModel->getAttributeLabel('night_of_overtime'),
            $itemModel->getAttributeLabel('basic_salary'),
            $itemModel->getAttributeLabel('cost_of_house'),
            $itemModel->getAttributeLabel('cost_of_food'),
            $itemModel->getAttributeLabel('count_of_children'),
            $itemModel->getAttributeLabel('cost_of_children'),
            $itemModel->getAttributeLabel('rate_of_year'),
            $itemModel->getAttributeLabel('hours_of_overtime'),
            $itemModel->getAttributeLabel('holiday_of_overtime'),
            $itemModel->getAttributeLabel('night_of_overtime'),
            $itemModel->getAttributeLabel('insurance'),
            $itemModel->getAttributeLabel('insurance_owner'),
            $itemModel->getAttributeLabel('tax'),
            $itemModel->getAttributeLabel('cost_of_trust'),
            $itemModel->getAttributeLabel('total_salary'),
            $itemModel->getAttributeLabel('advance_money'),
            $itemModel->getAttributeLabel('payment_salary'),
        ];

        foreach ($model->getSalaryPeriodItems()->all() as $index => $item) {
            /** @var SalaryPeriodItems $item */
            $rows[$index + 1] = [
                $item->user->nationalCode,
                $item->user->fullName,
                $item->hours_of_work,
                $item->holiday_of_overtime,
                $item->night_of_overtime,
                $item->basic_salary,
                $item->cost_of_house,
                $item->cost_of_food,
                $item->count_of_children,
                $item->cost_of_children,
                $item->rate_of_year,
                $item->hours_of_overtime,
                $item->holiday_of_overtime,
                $item->night_of_overtime,
                $item->insurance,
                $item->insurance_owner,
                $item->tax,
                $item->cost_of_trust,
                $item->total_salary,
                $item->advance_money,
                $item->payment_salary,
            ];
        }

        $fileName = $model->id . ' - ' . time() . '.csv';
        $csvExport = new CsvExport();
        $csvExport->array_to_csv_download($rows, $fileName);
    }


    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteAll($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canDeleteItems()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = SalaryPeriodItems::deleteAll(['period_id' => $model->id]);
                if ($flag) {
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
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
                $transaction->rollBack();
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Yii::t("app", "Error In Save Info")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionExportExcel($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT);
        $model->load(Yii::$app->request->post());
        $rows = [];
        $rows[0] = [];
        $totalCount = 0;
        $totalBalance = 0;;
        $document = Document::find()->findByModel($model->id)->findByType(Document::TYPE_SALARY_PERIOD_PAYMENT)->one();
        foreach ($document->getDocumentDetails()->all() as $index => $item) {
            /** @var DocumentDetails $item */
            if ($item->debtor > 0) {
                if (($employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $item->account->aCustomer->user->id])->one()) !== null) {
                    $salary_item = $model->getSalaryPeriodItems()->andWhere(['user_id' => $item->account->aCustomer->user->id, 'can_payment' => Helper::CHECKED])->limit(1)->one();
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
            $document = Document::find()->findByModel($model->id)->findByType(Document::TYPE_SALARY_PERIOD_PAYMENT)->one();
            foreach ($document->getDocumentDetails()->all() as $item) {
                /** @var DocumentDetails $item */
                if ($item->debtor > 0) {
                    if (($employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $item->account->aCustomer->user->id])->one()) !== null) {
                        $salary_item = $model->getSalaryPeriodItems()->andWhere(['user_id' => $item->account->aCustomer->user->id, 'can_payment' => Helper::CHECKED])->limit(1)->one();
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
                $document = Document::find()->findByModel($anotherPeriodModel->id)->findByType(Document::TYPE_SALARY_PERIOD_PAYMENT)->one();
                foreach ($document->getDocumentDetails()->all() as $item) {
                    /** @var DocumentDetails $item */
                    if ($item->debtor > 0) {
                        if (($employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $item->account->aCustomer->user->id])->one()) !== null) {
                            $salary_item = $anotherPeriodModel->getSalaryPeriodItems()->andWhere(['user_id' => $item->account->aCustomer->user->id, 'can_payment' => Helper::CHECKED])->limit(1)->one();
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

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionInsuranceWithNative($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT_INSURANCE);
        $model->loadDefaultValuesBeforeInsuranceExport();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $rows = [];

            $rows_header = [
                'DSK_ID' => $model->workshop->code,  // کد کارگاه
                'DSK_NAME' => $model->workshop->title, // نام کارگاه
                'DSK_FARM' => $model->workshop->manager, // نام کارفرما
                'DSK_ADRS' => $model->workshop->address, // آدرس
                'DSK_KIND' => $model->DSK_KIND, // نوع لیست form
                'DSK_YY' => Yii::$app->jdf->jdate("y", $model->start_date),  // سال
                'DSK_MM' => Yii::$app->jdf->jdate("m", $model->start_date),  // ماه
                'DSK_LISTNO' => $model->DSK_LISTNO, // شماره لیست form
                'DSK_DISC' => $model->DSK_DISC, // شرح لیست form
                'DSK_NUM' => $model->DSK_NUM, // تعداد کارکنان
                'DSK_TDD' => $model->DSK_TDD, //مجموع روز های کارکرد
                'DSK_TROOZ' => $model->DSK_TROOZ, // مجموع دستمزد روزانه
                'DSK_TMAH' => $model->DSK_TMAH, // مجموع دستمزد ماهانه
                'DSK_TMAZ' => $model->DSK_TMAZ, // مجموع مزایای ماهانه مشمول
                'DSK_TMASH' => $model->DSK_TMASH, // مجموع دستمزد مزایای ماهانه مشمول
                'DSK_TTOTL' => $model->DSK_TTOTL, // هجوَع کل مزایای  ماهانه (مشمولٍ غیر مشمول)
                'DSK_TBIME' => $model->DSK_TBIME,// مجموع حق بیمه کارمند
                'DSK_TKOSO' => $model->DSK_TKOSO, // مجموع حق بیمه کارفرما
                'DSK_BIC' => $model->DSK_BIC, // مجموع حق بیکاری
                'DSK_RATE' => $model->DSK_RATE, // نرخ حق بیمه
                'DSK_PRATE' => $model->DSK_PRATE, //نرخ پورسانت
                'DSK_BIMH' => $model->DSK_BIMH, //نرخ مشاغل سخت و زیان
                'MON_PYM' => $model->workshop->row, // ردیف پیمان
            ];
            foreach ($model->salaryPeriodItems as $index => $item) {
                $employee = $item->employee;
                $rows[] = [
                    'DSW_ID' => $model->workshop->code,  // کد کارگاه
                    'DSW_YY' => Yii::$app->jdf->jdate("y", $model->start_date),  // سال
                    'DSW_MM' => Yii::$app->jdf->jdate("m", $model->start_date),  // ماه
                    'DSW_LISTNO' => $model->id,  // شماره لیست
                    'DSW_ID1' => $employee->insurance_code,  // شماره بیمه
                    'DSW_FNAME' => $employee->first_name,  // نام
                    'DSW_LNAME' => $employee->last_name,  // نام خانوادگی
                    'DSW_DNAME' => $employee->father_name,  // نام پدر
                    'DSW_IDNO' => $employee->sh_number,  // شماره شناسنامه
                    'DSW_IDPLC' => $employee->issue_place,  // محل صدور
                    'DSW_IDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->issue_date),  // تاریخ صدور
                    'DSW_BDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->birthday),  // تاریخ تولد
                    'DSW_SEX' => $employee->sex,  // جنسیت
                    'DSW_NAT' => $employee->national,  // ملیت
                    'DSW_OCP' => $employee->salaryInsurance->group,  // شرح شفل
                    'DSW_SDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->start_work),  // شروع کار
                    'DSW_EDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->end_work),  // ترک کار
                    'DSW_DD' => $item->hours_of_work,  // نعداد روز کارکرد
                    'DSW_ROOZ' => $item->basic_salary,  // دستمزد روزانه
                    'DSW_MAH' => ($item->hours_of_work * $item->basic_salary),  // دستمزد ماهانه
                    'DSW_MAZ' => (($item->total_salary - $item->cost_of_children) - ($item->hours_of_work * $item->basic_salary)),  // مزایای ماهانه
                    'DSW_MASH' => ($item->total_salary - $item->cost_of_children),  // جمع دستمزد و مزایای ماهانه مشمول
                    'DSW_TOTL' => $item->total_salary,  // جمع کل دستمزد و مزایای ماهانه
                    'DSW_BIME' => $item->insurance,  // حق بیمه سهم بیمه شده
                    'DSW_PRATE' => 0,  // نرخ پور سانت
                    'DSW_JOB' => $employee->salaryInsurance->code,  // کد شغل
                    'PER_NATCOD' => $employee->nationalCode,  // کد ملی
                ];
            }
            $fileId = Yii::$app->jdf->jdate("ymd") . '00' . $model->file_number;
            $fileName = 'IR' . $model->shaba . $fileId . '.txt';
            MGLogs::saveManual(SalaryPeriod::class, $model->id, $rows);
            return [
                'success' => true,
                'msg' => Yii::t("app", "Item Created"),
                'html' => $this->renderAjax('_excel-insurance-send-to-native', [
                    'model' => $model,
                    'rows' => json_encode($rows),
                    'rows_header' => json_encode($rows_header),
                    'fileName' => $fileName,
                ]),
            ];

        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form-insurance-with-native', [
            'model' => $model,
        ]);
    }

    public function actionPrintInsuranceWithNative($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT_INSURANCE);
        $model->loadDefaultValuesBeforeInsuranceExport();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $rows = [];

            $variable = [
                'ListMonth' => Yii::$app->jdf->jdate("m", $model->start_date),  // ماه
                'ListYear' => Yii::$app->jdf->jdate("y", $model->start_date),  // سال
                'ListNumber' => $model->DSK_LISTNO, // شماره لیست form
                'RadifPeyman' => $model->workshop->row, // ردیف پیمان
                'WorkshopNumber' => $model->workshop->code,  // کد کارگاه
                'EmployerName' => $model->workshop->manager, // نام کارفرما
                'WorkshopAddress' => $model->workshop->address, // آدرس
                'CompanyName' => $model->workshop->title, // عنوان
            ];
            foreach ($model->salaryPeriodItems as $index => $item) {
                $employee = $item->employee;
                if ($employee) {
                    $rows[] = [
                        'Radif' => $index + 1,
                        'DSW_ID' => $model->workshop->code,  // کد کارگاه
                        'DSW_YY' => Yii::$app->jdf->jdate("y", $model->start_date),  // سال
                        'DSW_MM' => Yii::$app->jdf->jdate("m", $model->start_date),  // ماه
                        'DSW_LISTNO' => $model->id,  // شماره لیست
                        'DSW_ID1' => $employee->insurance_code,  // شماره بیمه
                        'DSW_FNAME' => $employee->first_name,  // نام
                        'DSW_LNAME' => $employee->last_name,  // نام خانوادگی
                        'DSW_DNAME' => $employee->father_name,  // نام پدر
                        'DSW_IDNO' => $employee->sh_number,  // شماره شناسنامه
                        'DSW_IDPLC' => $employee->issue_place,  // محل صدور
                        'DSW_IDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->issue_date),  // تاریخ صدور
                        'DSW_BDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->birthday),  // تاریخ تولد
                        'DSW_SEX' => $employee->sex,  // جنسیت
                        'DSW_NAT' => $employee->national,  // ملیت
                        'DSW_OCP' => $employee->salaryInsurance->group,  // شرح شفل
                        'DSW_SDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->start_work),  // شروع کار
                        'DSW_EDATE' => \Yii::$app->phpNewVer->strReplace('/', '', $employee->end_work),  // ترک کار
                        'DSW_DD' => $item->hours_of_work,  // نعداد روز کارکرد
                        'DSW_ROOZ' => $item->basic_salary,  // دستمزد روزانه
                        'DSW_MAH' => ($item->hours_of_work * $item->basic_salary),  // دستمزد ماهانه
                        'DSW_MAZ' => (($item->total_salary - $item->cost_of_children) - ($item->hours_of_work * $item->basic_salary)),  // مزایای ماهانه
                        'DSW_MASH' => ($item->total_salary - $item->cost_of_children),  // جمع دستمزد و مزایای ماهانه مشمول
                        'DSW_TOTL' => $item->total_salary,  // جمع کل دستمزد و مزایای ماهانه
                        'DSW_BIME' => $item->insurance,  // حق بیمه سهم بیمه شده
                        'DSW_PRATE' => 0,  // نرخ پور سانت
                        'DSW_JOB' => $employee->salaryInsurance->code,  // کد شغل
                        'PER_NATCOD' => $employee->nationalCode,  // کد ملی
                    ];
                } else {
                    Yii::error(($item->id ?? null), 'print-insurance-send-to-native');
                }
            }
            return [
                'success' => true,
                'msg' => Yii::t("app", "Item Created"),
                'html' => $this->renderAjax('_print-insurance-send-to-native', [
                    'model' => $model,
                    'variable' => json_encode($variable),
                    'rows' => json_encode($rows),
                ]),
            ];

        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form-insurance-with-native', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @param $type
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAddToPaymentList($id, $type)
    {
        $model = $this->findModel($id);
        $result = [
            'status' => false,
            'message' => Yii::t("app", "Error In Save Info")
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->can_payment = $type;
            $flag = $model->save(false);
            if ($flag) {
                $result = [
                    'status' => true,
                    'message' => Yii::t("app", "Item Updated")
                ];
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSendSmsPayment($id)
    {
        $model = $this->findModelPeriod($id);
        foreach ($model->salaryPeriodItems as $item) {
            /** @var SalaryPeriodItems $item */
            $item->sendSmsPayment();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model->sms_payment = 1;
            $flag = $model->save();
            if ($flag) {
                $transaction->commit();
                $this->flash("success", Yii::t('app', 'Item Confirmed'));

            } else {
                $transaction->rollBack();
                $this->flash("warning", Yii::t("app", "Error In Save Info"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            $this->flash('warning', $e->getMessage());
        }
        return $this->redirect(['index', 'id' => $id]);
    }

    /**
     * Finds the SalaryPeriodItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalaryPeriodItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalaryPeriodItems::findOne($id)) !== null && (int)$model->period->kind == SalaryPeriod::KIND_YEAR) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the SalaryPeriod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalaryPeriod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelPeriod($id)
    {
        if (($model = SalaryPeriod::findOne($id)) !== null && (int)$model->kind == SalaryPeriod::KIND_YEAR) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the EmployeeBranchUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeBranchUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelEmployee($id)
    {
        if (($model = EmployeeBranchUser::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
