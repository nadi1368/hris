<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\components\CsvExport;
use hesabro\changelog\models\MGLogs;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeBranchUserSearch;
use hesabro\hris\models\SalaryItemsAddition;
use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\models\SalaryPeriodItemsSearch;
use hesabro\hris\models\TaxModel;
use hesabro\hris\Module;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii2tech\spreadsheet\Spreadsheet;

/**
 * SalaryPeriodItemsController implements the CRUD actions for SalaryPeriodItems model.
 */
class SalaryPeriodItemsController extends Controller
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
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($id)
    {
        $salaryPeriod = $this->findModelPeriod($id);
        $searchModel = new SalaryPeriodItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $salaryPeriod->id);

        $userIds = SalaryPeriodItems::find()->select(['user_id'])->andWhere(['period_id' => $salaryPeriod->id]);
        $searchModelUser = new EmployeeBranchUserSearch();
        $dataProviderUser = $searchModelUser->searchSalary(Yii::$app->request->queryParams, $userIds);

        return $this->render('index', [
            'salaryPeriod' => $salaryPeriod,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModelUser' => $searchModelUser,
            'dataProviderUser' => $dataProviderUser,
            'COST_HOURS_OVERTIME' => $salaryPeriod->year->COST_HOURS_OVERTIME
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUser($id)
    {
        $user = $this->findModelUser($id);
        $searchModel = new SalaryPeriodItemsSearch();
        $searchModel->user_id = $user->id;
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams);
        return $this->render('user', [
            'user' => $user,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $this->layout = '@hesabro/hris/views/layouts/print-bootstrap';
        $salaryPeriod = $this->findModelPeriod($id);
        $items = $salaryPeriod->getSalaryPeriodItems()->all();
        return $this->render('print', [
            'salaryPeriod' => $salaryPeriod,
            'items' => $items,
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionPrintSingleItem($id): string
    {
        $this->layout = '@hesabro/hris/views/layouts/print-bootstrap';
        $salaryPeriodItem = $this->findModel($id);
        if (!$salaryPeriodItem->canPrint()) {
            throw new ForbiddenHttpException('امکان چاپ وجود ندارد');
        }
        return $this->render('print-single-item', [
            'model' => $salaryPeriodItem,
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
        if (!$salaryPeriod->canCreateItems() || !$employee->canCreateSalaryPayment()) {
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation"));
        }
        $model = new SalaryPeriodItems([
            'scenario' => SalaryPeriodItems::SCENARIO_CREATE,
            'period_id' => $salaryPeriod->id,
            'user_id' => $employee->user_id,
        ]);

        $model->loadDefaultValuesBeforeCreate();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save();
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Created")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => Module::t('module', "Error In Save Info")
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
            return $result;
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'salaryPeriod' => $salaryPeriod,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SalaryPeriodItems model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation"));
        }
        $salaryPeriod = $model->period;
        $model->setScenario(SalaryPeriodItems::SCENARIO_UPDATE);

        $model->loadDefaultValuesBeforeUpdate();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save();
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Updated")
                    ];
                    if (Yii::$app->request->post('TypeSubmit') == 'next' && ($nextModel = $salaryPeriod->getSalaryPeriodItems()->andWhere(['<', SalaryPeriodItems::tableName() . '.id', $model->id])->orderBy([SalaryPeriodItems::tableName() . '.id' => SORT_DESC])->limit(1)->one()) !== null) {
                        $result['CallFunction'] = 'updateNextSalaryPeriodItems';
                        $result['ModalUrl'] = Url::to(['update', 'id' => $nextModel->id]);
                        $result['ModalTitle'] = Module::t('module', 'Update') . ' - ' . $nextModel->user->fullName;
                    }
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => Module::t('module', "Error In Save Info")
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
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation"));
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
                        'msg' => Module::t('module', "Item Updated")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => Module::t('module', "Error In Save Info")
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
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete() && $model->delete()) {
            $result = [
                'status' => true,
                'message' => Module::t('module', "Item Deleted")
            ];
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "Error In Save Info")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }


    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPreConfirm($id)
    {
        $model = $this->findModelPeriod($id);

        $varianceAdvanceMoney = SalaryPeriodItems::find()->andWhere(['period_id' => $model->id])->varianceAdvanceMoney()->all();
        $varianceSalaryItemsAddition = SalaryItemsAddition::find()
            ->joinWith(['salaryItems' => function (ActiveQuery $query) use ($model) {
                return $query->andWhere([SalaryPeriodItems::tableName() . '.period_id' => $model->id]);
            }])
            ->confirm()
            ->andWhere(['between', SalaryItemsAddition::tableName() . '.from_date', $model->start_date, $model->end_date])
            ->andWhere(SalaryItemsAddition::tableName() . '.changed >= ' . SalaryPeriodItems::tableName() . '.changed')
            ->all();
        $varianceComfortItems = ComfortItems::find()
            ->joinWith(['salaryItems' => function (ActiveQuery $query) use ($model) {
                return $query->andWhere([SalaryPeriodItems::tableName() . '.period_id' => $model->id]);
            }])
            ->confirm()
            ->andWhere(['between', ComfortItems::tableName() . '.created', $model->start_date, strtotime("+1 DAY", $model->end_date)])
            ->andWhere(ComfortItems::tableName() . '.changed >= ' . SalaryPeriodItems::tableName() . '.changed')
            ->all();

        $varianceHoursOfLowTime = SalaryPeriodItems::find()->andWhere(['period_id' => $model->id])->andWhere(['>', 'JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.hoursOfLowTime")', 0])->all();

        return $this->renderAjax('_pre-confirm', [
            'model' => $model,
            'varianceAdvanceMoney' => $varianceAdvanceMoney,
            'varianceSalaryItemsAddition' => $varianceSalaryItemsAddition,
            'varianceComfortItems' => $varianceComfortItems,
            'varianceHoursOfLowTime' => $varianceHoursOfLowTime,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canConfirm()) {
            $transaction = \Yii::$app->db->beginTransaction();
            $err_msg = '';
            try {
                $model->status = SalaryPeriod::STATUS_CONFIRM;
                $flag = $model->save(false);
                $flag = $flag && $model->saveMutex(SalaryPeriod::MutexSalaryPeriodConfirm, $model->id);
                if ($model->getSalaryPeriodItems()->exists()) {
                    $flag = $flag && $model->saveDocumentConfirm();
                    $flag = $flag && $model->saveDocumentAdvanceMoney();
                    $flag = $flag && $model->saveDocumentNonCashPayment();
                    $flag = $flag && $model->saveDocumentInsuranceAddition();
                    $flag = $flag && $model->saveDecreasePoint();
                }
                if ($flag) {
                    $transaction->commit();
                    $this->flash('success', Module::t('module', "Item Confirmed"));
                } else {
                    $transaction->rollBack();
                    $this->flash('warning', $err_msg ?: Module::t('module', "Error In Save Info"));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $this->flash('warning', $e->getMessage());
            }
        } else {
            $this->flash('warning', Module::t('module', "It is not possible to perform this operation"));
        }

        return $this->redirect(['index', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * لغو تایید
     */
    public function actionReturnConfirm($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canReturnConfirm()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_WAIT_CONFIRM;
                $flag = $model->save(false);
                $flag = $flag && $model->deleteMutex(SalaryPeriod::MutexSalaryPeriodConfirm, $model->id);
                $flag = $flag && $model->deleteDocument(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD);
                $flag = $flag && $model->deleteDocument(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD_ADVANCE_MONEY);
                $flag = $flag && $model->deleteDocument(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD_NON_CASH_PAYMENT);
                $flag = $flag && $model->deleteDocument(SalaryPeriod::DOCUMENT_TYPE_SALARY_INSURANCE_ADDITION);
                $flag = $flag && $model->deleteDecreasePoint();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Confirmed")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Module::t('module', "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "It is not possible to perform this operation")
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
            $this->flash('danger', Module::t('module', "It is not possible to perform this operation"));
            return $this->redirect(['index', 'id' => $id]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_PAYMENT;
                $flag = $model->save(false);
                $flag = $flag && $model->saveMutex(SalaryPeriod::MutexSalaryPeriodPayment, $model->id);
                if ($model->getSalaryPeriodItems()->exists()) {
                    $flag = $flag && $model->saveDocumentPayment();
                }
                $flag = $flag && $model->addEndWorkEmployee();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Created")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => Module::t('module', "Error In Save Info")
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
     *لفو پرداخت
     */
    public function actionReturnPayment($id)
    {
        $model = $this->findModelPeriod($id);

        if ($model->canReturnPayment()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = SalaryPeriod::STATUS_CONFIRM;
                $flag = $model->save(false);
                $flag = $flag && $model->deleteMutex(SalaryPeriod::MutexSalaryPeriodPayment, $model->id);
                $flag = $flag && $model->deleteDocument(SalaryPeriod::DOCUMENT_TYPE_SALARY_PERIOD_PAYMENT);
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Confirmed")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Module::t('module', "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "It is not possible to perform this operation")
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
                        'message' => Module::t('module', "Item Deleted")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Module::t('module', "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $transaction->rollBack();
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "Error In Save Info")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }


    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCopyFromPreviousPeriod($id)
    {
        $period = $this->findModelPeriod($id);
        $previousModel = SalaryPeriod::find()->byPrevious($period->workshop_id, $period->start_date)->limit(1)->one();
        if ($period->canCopyPreviousPeriod() && $previousModel !== null) {
            try {
                $countCopy = 0;
                $countExist = 0;
                $msg_error = '';
                foreach ($previousModel->getSalaryPeriodItems()->all() as $item) {
                    /** @var SalaryPeriodItems $item */
                    $employee = $item->employee;
                    if ($employee === null) {
                        $msg_error .= Html::tag('p', 'اطلاعات کارمندی پاک شده است.' . $item->user->fullName);
                        continue;
                    }
                    $countExist++;
                    if ($period->canCreateItems() && $employee->canCreateSalaryPayment()) {
                        $transaction = \Yii::$app->db->beginTransaction();
                        try {
                            $model = new SalaryPeriodItems([
                                'scenario' => SalaryPeriodItems::SCENARIO_CREATE,
                                'period_id' => $period->id,
                                'user_id' => $employee->user_id,
                            ]);
                            $model->loadDefaultValuesBeforeCopy();
                            if ($model->hours_of_work > 0 && (empty($employee->end_work) || $employee->end_work > Yii::$app->jdf->jdate("Y/m/d", $period->start_date))) {
                                if ($model->save()) {
                                    $countCopy++;
                                } else {
                                    $msg_error .= Html::tag('p', $employee->user->fullName);
                                    $msg_error .= Html::errorSummary($model, ['header' => '']);
                                }
                            }
                            $transaction->commit();
                        } catch (\Exception $e) {
                            Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                            $transaction->rollBack();
                        }
                    } else {
                        $msg_error .= Html::tag('p', $employee->user->fullName);
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
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $this->flash('danger', $e->getMessage());
            }
        } else {
            $this->flash('danger', Module::t('module', 'It is not possible to perform this operation.'));
        }
        return $this->redirect(['index', 'id' => $id]);
    }

    /**
     * @param $id
     * @return array|string
     * @throws HttpException
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
            foreach ($model->salaryPeriodItems as $index => $item) {
                if ($item->finalPayment > 0) {
                    if (($employeeUser = $item->employee) !== null) {
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
                            $item->finalPayment,
                            $employeeUser->user->fullName,
                            "پرداختی حقوق " . $model->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $model->start_date),
                        ];
                        $totalBalance += $item->finalPayment;
                        $totalCount++;
                    } else {
                        throw new HttpException(400, $item->user->fullName . ' در مشخصات کارمندی ثبت نشده است.');
                    }

                }
            }

            foreach (is_array($model->another_period) ? $model->another_period : [] as $anotherPeriodId) {
                $anotherPeriodModel = $this->findModelPeriod($anotherPeriodId);
                foreach ($anotherPeriodModel->salaryPeriodItems as $index => $item) {
                    if ($item->finalPayment > 0) {
                        if (($employeeUser = $item->employee) !== null) {
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
                                $item->finalPayment,
                                $employeeUser->user->fullName,
                                "پرداختی حقوق " . $anotherPeriodModel->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $anotherPeriodModel->start_date),
                            ];
                            $totalBalance += $item->finalPayment;
                            $totalCount++;
                        } else {
                            throw new HttpException(400, $item->user->fullName . ' در مشخصات کارمندی ثبت نشده است.');
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
            MGLogs::saveManual(SalaryPeriod::OLD_CLASS_NAME, $model->id, $rows);
            return [
                'success' => true,
                'msg' => Module::t('module', "Item Created"),
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
     * خروجی بیمه با ابرسا
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
                if ($employee === null) {
                    return [
                        'success' => false,
                        'msg' => "اطلاعات کارمندی " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                if ($employee->salaryInsurance === null) {
                    return [
                        'success' => false,
                        'msg' => "کد شغلی کارمند " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                if (empty($employee->insurance_code)) {
                    return [
                        'success' => false,
                        'msg' => "اطلاعات بیمه ای " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
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
                    'DSW_EDATE' => $employee->end_work > 1 && $item->total_salary == 0 ? \Yii::$app->phpNewVer->strReplace('/', '', Yii::$app->jdf::plusDay($employee->end_work, 1)) : '',  // ترک کار
                    'DSW_DD' => $item->hours_of_work,  // نعداد روز کارکرد
                    'DSW_ROOZ' => $item->basic_salary,  // دستمزد روزانه
                    'DSW_MAH' => ($item->hours_of_work * $item->basic_salary),  // دستمزد ماهانه
                    'DSW_MAZ' => $employee->manager ? 0 : (($item->total_salary - $item->cost_of_children - $item->non_cash_commission) - ($item->hours_of_work * $item->basic_salary)),  // مزایای ماهانه
                    'DSW_MASH' => $employee->manager ? ($item->hours_of_work * $item->basic_salary) : ($item->total_salary - $item->cost_of_children - $item->non_cash_commission),  // جمع دستمزد و مزایای ماهانه مشمول
                    'DSW_TOTL' => $employee->manager ? ($item->hours_of_work * $item->basic_salary) : $item->total_salary,  // جمع کل دستمزد و مزایای ماهانه
                    'DSW_BIME' => $item->insurance,  // حق بیمه سهم بیمه شده
                    'DSW_PRATE' => 0,  // نرخ پور سانت
                    'DSW_JOB' => $employee->salaryInsurance->code,  // کد شغل
                    'PER_NATCOD' => $employee->nationalCode,  // کد ملی
                ];
            }
            $fileId = Yii::$app->jdf->jdate("ymd") . '00' . $model->file_number;
            $fileName = 'IR' . $model->shaba . $fileId . '.txt';
            MGLogs::saveManual(SalaryPeriod::OLD_CLASS_NAME, $model->id, $rows);
            return [
                'success' => true,
                'msg' => Module::t('module', "Item Created"),
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

    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     * خروجی بیمه با ابرسا و ست شدن همه به عنوان ترک کار
     */
    public function actionInsuranceWithNativeSetEndWork($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT_INSURANCE);
        $model->loadDefaultValuesBeforeInsuranceExport(true);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $rows = [];

            $rows_header = [
                'DSK_ID' => $model->workshop->code,  // کد کارگاه
                'DSK_NAME' => $model->workshop->title, // نام کارگاه
                'DSK_FARM' => $model->workshop->manager, // نام کارفرما
                'DSK_ADRS' => $model->workshop->address, // آدرس
                'DSK_KIND' => $model->DSK_KIND, // نوع لیست form
                'DSK_YY' => Yii::$app->jdf->jdate("y", strtotime("+1 DAY", $model->end_date)),  // سال
                'DSK_MM' => Yii::$app->jdf->jdate("m", strtotime("+1 DAY", $model->end_date)),  // ماه
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
                if ($item->total_salary == 0) {
                    continue;
                }
                $employee = $item->employee;
                if ($employee === null) {
                    return [
                        'success' => false,
                        'msg' => "اطلاعات کارمندی " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                if ($employee->salaryInsurance === null) {
                    return [
                        'success' => false,
                        'msg' => "کد شغلی کارمند " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                if (empty($employee->insurance_code)) {
                    return [
                        'success' => false,
                        'msg' => "اطلاعات بیمه ای " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                $rows[] = [
                    'DSW_ID' => $model->workshop->code,  // کد کارگاه
                    'DSW_YY' => Yii::$app->jdf->jdate("y", strtotime("+1 DAY", $model->end_date)),  // سال
                    'DSW_MM' => Yii::$app->jdf->jdate("m", strtotime("+1 DAY", $model->end_date)),  // ماه
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
                    'DSW_EDATE' => \Yii::$app->phpNewVer->strReplace('/', '', Yii::$app->jdf::plusDay(Yii::$app->jdf->jdate("Y/m/d", $model->end_date), 1)),  // ترک کار
                    'DSW_DD' => 0,  // نعداد روز کارکرد
                    'DSW_ROOZ' => 0,  // دستمزد روزانه
                    'DSW_MAH' => 0,  // دستمزد ماهانه
                    'DSW_MAZ' => 0,  // مزایای ماهانه
                    'DSW_MASH' => 0,  // جمع دستمزد و مزایای ماهانه مشمول
                    'DSW_TOTL' => 0,  // جمع کل دستمزد و مزایای ماهانه
                    'DSW_BIME' => 0,  // حق بیمه سهم بیمه شده
                    'DSW_PRATE' => 0,  // نرخ پور سانت
                    'DSW_JOB' => $employee->salaryInsurance->code,  // کد شغل
                    'PER_NATCOD' => $employee->nationalCode,  // کد ملی
                ];
            }
            $fileId = Yii::$app->jdf->jdate("ymd") . '00' . $model->file_number;
            $fileName = 'IR' . $model->shaba . $fileId . '.txt';
            return [
                'success' => true,
                'msg' => Module::t('module', "Item Created"),
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

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTaxWithNative($id)
    {
        $model = $this->findModelPeriod($id);
        $rows = [];
        $rows_header = [];
        foreach ($model->salaryPeriodItems as $index => $item) {
            if ($item->hours_of_work > 0) {
                $employee = $item->employee;
                if ($employee === null) {
                    throw new NotFoundHttpException("اطلاعات کارمندی " . $item->user->fullName . " ثبت نشده است.");
                }
                if (empty($employee->nationalCode)) {
                    throw new NotFoundHttpException("کد ملی " . $item->user->fullName . " ثبت نشده است.");
                }
                $otherPayment = $item->cost_of_trust + $item->getHolidayOfOvertimeCost() + $item->getNightOfOvertimeCost(); // سایر پرداختهای غیر مستمر نقدی ماه جاری
                $commission = $item->commission; // پاداشهای موردی ماه جاری
                $lowCost = $item->getHoursOfLowTimeCost();
                if ($lowCost > 0) {
                    if ($lowCost <= $otherPayment) {
                        $otherPayment -= $lowCost;
                    } elseif ($lowCost <= $commission) {
                        $commission -= $lowCost;
                    } else {
                        throw new NotFoundHttpException("کارمند " . $item->user->fullName . " دارای کسر کار منفی می باشد.");
                    }
                }
                $rows[] = [
                    0 => $employee->nationalCode, // کد ملی/ کد فراگیر
                    1 => TaxModel::PAYMENT_TYPE_RIAL, // نوع پرداخت
                    2 => TaxModel::WORK_PLACE_NORMAL, //وضعیت محل خدمت
                    3 => TaxModel::EXCEPTION_NORMAL, //استثنائات موضوع قانون بودجه 1403
                    4 => TaxModel::TYPE_RIAL, // نوع ارز
                    5 => 1, // نرخ تسعیر ارز
                    6 => (($item->hours_of_work * $item->basic_salary) + $item->cost_of_house + $item->cost_of_food + $item->cost_of_spouse + $item->cost_of_children + $item->rate_of_year), // مبلغ جمع ناخالص حقوق و مزایای مستمر نقدی ماه جاری - ریالی
                    7 => '', // پرداختهای مستمر معوق که مالیاتی برای آنها محاسبه نشده است- ریالی
                    8 => TaxModel::FURNITURE_NONE, // مسکن )نحوه استفاده از مسکن(
                    9 => '', // مبلغ کسر شده از حقوق کارمند بابت مسکن ماه جاری - ریالی
                    10 => TaxModel::VEHICLE_NONE, // اتومبیل اختصاصی )نحوه استفاده از اتومبیل(
                    11 => '', // مبلغ کسر شده از حقوق کارمند بابت اتومبیل اختصاصی ماه جاری- ریالی
                    12 => '', // پرداخت مزایای مستمر غیر نقدی ماه جاری- ریالی
                    13 => '', // مبلغ حقوق و مزایای مستمر غیرنقدی معوق که مالیاتی برای آنها محاسبه نشده است- ریالی
                    14 => '', // مبلغ حق الزحمه/حق مشاوره/حق حضور/حق التدریس/حق التحقیق / حق پژوهش- ریالی
                    15 => '', // مبلغ قراردادهای پژوهشی- ریالی
                    16 => $item->getHoursOfOvertimeCost(), // ناخالص اضافه کاری ماه جاری- ریالی
                    17 => '', // هزینه سفر- ریالی
                    18 => '', //  فوق العاده مسافرت )ماموریت( - ریالی
                    19 => '', //  کارانه- ریالی
                    20 => $commission, // پاداش )به استثنای پاداش آخر سال و پاداش پایان خدمت( - ریالی
                    21 => '',  // پاداش آخر سال- ریالی
                    22 => '', // عیدی ساالنه- ریالی
                    23 => '', // پاداش پایان خدمت- ریالی
                    24 => '', // خسارت اخراج- ریالی
                    25 => '', // بازخرید خدمت - ریالی
                    26 => '', // حق سنوات- ریالی
                    27 => '', // حقوق ایام مرخصی استفاده نشده- ریالی
                    28 => $otherPayment,  // سایر حقوق و مزایای غیر مستمر نقدی ماه جاری- ریالی
                    29 => '',//(int)($item->year->COST_TAX_STEP_1_MIN * $item->hours_of_work / $item->period->countDay), // مبلغ حقوق و مزایای غیرمستمر نقدی معوق که مالیاتی برای آنها محاسبه نشده است- ریالی
                    30 => $item->non_cash_commission, // مبلغ قیمت تمام شده مزایای غیر مستمر غیرنقدی ماه جاری- ریالی
                    31 => '', // مبلغ مزایای غیر مستمر غیر نقدی معوق که مالیاتی برای آنها محاسبه نشده است- ریالی
                    32 => $item->insurance, // حق بیمه های درمان موضوع ماده 137 ق.م.م.
                    33 => '', // حق بیمه های عمر و زندگی موضوع ماده 137 ق.م.م
                    34 => (int)($item->payment_salary), // خالص پرداختی به حقوق بگیر
                ];
            }
        }
        $rows_header = [
            Yii::$app->jdf->jdate("Y", $model->end_date), // سال
            Yii::$app->jdf->jdate("m", $model->end_date), // ماه
            0, // دهی مالیاتی ماه جاری
            0, //  بدهی مالیاتی ماه گذشته
            str_replace('/', '', $model->end_date), // تاریخ ثبت در دفتر روزنامه
            TaxModel::TYPE_OF_PAYMENT_BANK, // نحوه پرداخت
            '',
            '',
            '',
            '', // شعبه بانک
            '', // شماره حساب
            '', // مبلغ پرداختی
            '', // تاریخ پرداخت
            '', // مبلغ پرداختی خزانه
        ];
        $fileName = 'WH' . Yii::$app->jdf->jdate("Ym", $model->end_date) . '.txt';
        $fileNameHeader = 'WK' . Yii::$app->jdf->jdate("Ym", $model->end_date) . '.txt';
        MGLogs::saveManual(SalaryPeriod::OLD_CLASS_NAME, $model->id, $rows);
        return $this->renderAjax('_excel-tax-send-to-native', [
            'model' => $model,
            'rows' => json_encode($rows),
            'fileName' => $fileName,
            'fileNameHeader' => $fileNameHeader,
            'rows_header' => json_encode($rows_header),
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

                if ($employee === null) {
                    return [
                        'success' => false,
                        'msg' => "اطلاعات کارمندی " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                if ($employee->salaryInsurance === null) {
                    return [
                        'success' => false,
                        'msg' => "کد شغلی کارمند " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
                if (empty($employee->insurance_code)) {
                    return [
                        'success' => false,
                        'msg' => "اطلاعات بیمه ای " . $item->user->fullName . " ثبت نشده است.",
                    ];
                }
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
                    'DSW_EDATE' => $employee->end_work > 1 && $item->total_salary == 0 ? \Yii::$app->phpNewVer->strReplace('/', '', Yii::$app->jdf::plusDay($employee->end_work, 1)) : '',  // ترک کار
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
            return [
                'success' => true,
                'msg' => Module::t('module', "Item Created"),
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
            'message' => Module::t('module', "Error In Save Info")
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->can_payment = $type;
            $flag = $model->save(false);
            if ($flag) {
                $result = [
                    'status' => true,
                    'message' => Module::t('module', "Item Updated")
                ];
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCheckDocument($id)
    {
        $salaryPeriod = $this->findModelPeriod($id);
        $find = false;
        $link = [];
        $link['SalaryPeriodItemsSearch']['user_id'] = [];
        foreach ($salaryPeriod->salaryPeriodItems as $item) {
            $totalDebtor = $item->total_salary + $item->insurance_owner;
            $totalCreditor = $item->insurance + $item->insurance_owner + $item->tax + $item->payment_salary;
            if ($totalDebtor - $totalCreditor !== 0) {
                $link['SalaryPeriodItemsSearch']['user_id'][] = $item->user_id;
                $find = true;
            }
        }
        if ($find) {
            $this->flash('danger', 'لطفا اطلاعات کارمندان زیر را بررسی نمایید.');
        } else {
            $this->flash('success', 'سند مورد نظر صحیح است');
        }
        return $this->redirect(ArrayHelper::merge(['index', 'id' => $id], $link));
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
        if (($model = SalaryPeriodItems::findOne($id)) !== null && (int)$model->period->kind == SalaryPeriod::KIND_SALARY) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
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
        if (($model = SalaryPeriod::findOne($id)) !== null && (int)$model->kind == SalaryPeriod::KIND_SALARY) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
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

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return object the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelUser($id)
    {
        if (($model = Module::getInstance()->user::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }

    /**
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionExport($id)
    {
        $model = $this->findModelPeriod($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getSalaryPeriodItems(),
        ]);

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => 'کد ملی',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->user->nationalCode;
                    }
                ],
                [
                    'label' => 'نام و نام خانوادگی',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->user->fullName;
                    }
                ],
                'hours_of_work',
                'treatment_day',
                'basic_salary',
                [
                    'label' => 'حقوق پایه',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->hours_of_work * $item->basic_salary;
                    }
                ],
                'cost_of_house',
                'cost_of_food',
                'cost_of_spouse',
                'cost_of_children',
                'rate_of_year',
                [
                    'label' => 'اضافه کاری',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->getHoursOfOvertimeCost();
                    }
                ],
                [
                    'label' => 'تعطیل کاری',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->getHolidayOfOvertimeCost();
                    }
                ],
                [
                    'label' => 'شب کاری',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->getNightOfOvertimeCost();
                    }
                ],
                [
                    'label' => 'کسر کاری',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->getHoursOfLowTimeCost();
                    }
                ],
                'cost_of_trust',
                'commission',
                'total_salary',
                'insurance_owner',
                'insurance',
                'tax',
                'advance_money',
                'payment_salary',
                [
                    'label' => 'پرداختی',
                    'value' => function (SalaryPeriodItems $item) {
                        return $item->finalPayment;
                    }
                ],
            ],
        ]);
        return $exporter->send($model->title . ' - ' . Yii::$app->jdf->jdate("Y", $model->start_date) . '.xls');
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
        foreach ($model->salaryPeriodItems as $index => $item) {
            if ($item->finalPayment > 0) {
                if (($employeeUser = $item->employee) !== null) {
                    if ($item->can_payment !== null) {
                        $rows[$index + 1] = [
                            'IR' . $employeeUser->shaba,
                            '',
                            $item->finalPayment,
                            $employeeUser->user->fullName,
                            "پرداختی حقوق " . $model->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $model->start_date),
                        ];
                        $totalBalance += $item->finalPayment;
                        $totalCount++;
                    }
                } else {
                    throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.=>' . $item->id));
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
     * @return array|string|Response
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     * خروجی مزایای غیر نقدی
     */
    public function actionExcelBankNonCashWithNative($id)
    {
        $model = $this->findModelPeriod($id);
        $model->setScenario(SalaryPeriod::SCENARIO_EXPORT_NONE_CASH);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $rows = [];
            $IBANList = [];
            $accountList = [];
            $totalCount = 0;
            $totalBalance = 0;
            $index = 1;
            foreach ($model->getSalaryPeriodItems()->andWhere('non_cash_commission>0')->all() as $item) {
                /** @var SalaryPeriodItems $item */
                $employeeUser = $item->employee;
                if ($employeeUser !== null) {
                    if (empty($employeeUser->shaba_non_cash)) {
                        throw new HttpException(400, $employeeUser->user->fullName . ' : شماره کارت کارانه این کارمند ثبت نشده است');
                    }
                    if (empty($employeeUser->account_non_cash)) {
                        throw new HttpException(400, $employeeUser->user->fullName . ' : شماره حساب کارانه این کارمند ثبت نشده است');
                    }
                    if (in_array($employeeUser->shaba_non_cash, $IBANList)) {
                        throw new HttpException(400, $employeeUser->user->fullName . ' : شماره کارت کارانه این کارمند تکراری است');
                    }
                    if (in_array($employeeUser->account_non_cash, $accountList)) {
                        throw new HttpException(400, $employeeUser->user->fullName . ' : شماره حساب کارانه این کارمند تکراری است');
                    }
                    $IBANList[] = $employeeUser->shaba_non_cash;
                    $accountList[] = $employeeUser->account_non_cash;
                    $rows[$index++] = [
                        'firstName' => $employeeUser->user->first_name,
                        'lastName' => $employeeUser->user->last_name,
                        'nationalCode' => $employeeUser->user->nationalCode,
                        'cartNumber' => $employeeUser->shaba_non_cash,
                        'accountNumber' => $employeeUser->account_non_cash,
                        'amount' => $item->non_cash_commission,
                        'description' => "مزایای غیر نقدی " . $model->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $model->start_date)
                    ];
                    $totalBalance += $item->non_cash_commission;
                    $totalCount++;
                } else {
                    Yii::error($item->employee->user_id . '=>' . $item->employee->user->getFullName() . ' در مشخصات کارمندی ثبت نشده است.', Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                    throw new HttpException(400, $item->employee->user->getFullName() . ' در مشخصات کارمندی ثبت نشده است.');
                }
            }

            foreach (is_array($model->another_period) ? $model->another_period : [] as $anotherPeriodId) {
                $anotherPeriodModel = $this->findModelPeriod($anotherPeriodId);
                foreach ($model->getSalaryPeriodItems()->andWhere('non_cash_commission>0')->all() as $item) {
                    /** @var SalaryPeriodItems $item */
                    $employeeUser = $item->employee;
                    if ($employeeUser !== null) {
                        if (empty($employeeUser->shaba_non_cash)) {
                            throw new HttpException(400, $employeeUser->user->fullName . ' : شماره کارت کارانه این کارمند ثبت نشده است');
                        }
                        if (empty($employeeUser->account_non_cash)) {
                            throw new HttpException(400, $employeeUser->user->fullName . ' : شماره حساب کارانه این کارمند ثبت نشده است');
                        }
                        if (in_array($employeeUser->shaba_non_cash, $IBANList)) {
                            throw new HttpException(400, $employeeUser->user->fullName . ' : شماره کارت کارانه این کارمند تکراری است');
                        }
                        if (in_array($employeeUser->account_non_cash, $accountList)) {
                            throw new HttpException(400, $employeeUser->user->fullName . ' : شماره حساب کارانه این کارمند تکراری است');
                        }
                        $IBANList[] = $employeeUser->shaba_non_cash;
                        $accountList[] = $employeeUser->account_non_cash;
                        $rows[$index++] = [
                            'firstName' => $employeeUser->user->first_name,
                            'lastName' => $employeeUser->user->last_name,
                            'nationalCode' => $employeeUser->user->nationalCode,
                            'cartNumber' => $employeeUser->shaba_non_cash,
                            'accountNumber' => $employeeUser->account_non_cash,
                            'amount' => $item->non_cash_commission,
                            'description' => "مزایای غیر نقدی " . $anotherPeriodModel->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $anotherPeriodModel->start_date)
                        ];
                        $totalBalance += $item->non_cash_commission;
                        $totalCount++;
                    } else {
                        Yii::error($item->employee->user_id . '=>' . $item->employee->user->getFullName() . ' در مشخصات کارمندی ثبت نشده است.', Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                        throw new HttpException(400, $item->employee->user->getFullName() . ' در مشخصات کارمندی ثبت نشده است.');
                    }
                }
            }
            MGLogs::saveManual(SalaryPeriod::OLD_CLASS_NAME, $model->id, $rows);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $rows,
            ]);
            $spreadsheet = new Spreadsheet([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'ردیف',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $index + 1;
                        },
                    ],
                    [
                        'attribute' => 'نام',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['firstName'];
                        },
                    ],
                    [
                        'attribute' => 'نام خانوادگی',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['lastName'];
                        },
                    ],
                    [
                        'attribute' => 'کد ملی',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['nationalCode'];
                        },
                    ],
                    [
                        'attribute' => 'شماره کارت',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['cartNumber'];
                        },
                    ],
                    [
                        'attribute' => 'شماره حساب',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['accountNumber'];
                        },
                    ],
                    [
                        'attribute' => 'مبلغ',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['amount'];
                        },
                    ],
                    [
                        'attribute' => 'توضیحات',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return $model['description'];
                        },
                    ],
                ],
            ]);

            return $spreadsheet->send('ExcelNoneCash_' . Yii::$app->client->identity->domain . '_' . Yii::$app->jdf->jdate("Y/m/d", $model->start_date) . '.xlsx');
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form-bank-none-cash', [
            'model' => $model,
        ]);
    }
}