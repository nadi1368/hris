<?php

namespace hesabro\hris\controllers;

use backend\models\YearSearch;
use common\models\Year;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\EmployeeBranch;
use hesabro\hris\models\EmployeeBranchSearch;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeBranchUserSearch;
use hesabro\hris\models\EmployeeChild;
use hesabro\hris\models\EmployeeExperience;
use hesabro\hris\models\EmployeeHistory;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class EmployeeBranchController extends Controller
{
    use AjaxValidationTrait;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->layout = Module::getInstance()->layout;
    }

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
                    'reject-update' => ['POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['EmployeeBranch/index', 'superadmin'],
                            'actions' => ['index', 'users', 'view', 'view-user', 'view-user-documents', 'view-user-contracts', 'confirm-document', 'delete-document', 'user-detail']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['EmployeeBranch/create', 'superadmin'],
                            'actions' => ['create']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['EmployeeBranch/update', 'superadmin'],
                            'actions' => ['update', 'update-user', 'insurance-data', 'reject-update', 'year-setting', 'update-year-setting', 'set-end-work', 'start-work-again', 'return-end-work', 'change-branch']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['EmployeeBranch/delete', 'superadmin'],
                            'actions' => ['delete', 'migrate']
                        ],
                    ]
            ]
        ];
    }


    /**
     * Lists all EmployeeBranch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeBranchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all EmployeeBranch models.
     * @return mixed
     */
    public function actionUsers()
    {
        $searchModel = new EmployeeBranchUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $branch_id
     * @param $user_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewUser($user_id)
    {
        $model = $this->findModelUser($user_id);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view-user', [
                'model' => $model,
            ]);
        } else {
            return $this->render('view-user', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays a single EmployeeBranch model.
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
     * Creates a new EmployeeBranch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmployeeBranch();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                $flag = $flag && $model->createUser();
                if ($flag) {
                    $transaction->commit();
                    $this->flash("success", Module::t('module', 'Item Created'));
                    return $this->redirect(['index', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Module::t('module', "Error In Save Info"));
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmployeeBranch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            $this->flash('danger', Module::t('module', "Can Not Update"));
            return $this->redirect(['index']);
        }

        $branch_users = $model->getBranchUsers()->all();
        $old_user_ids = ArrayHelper::map($branch_users, 'user_id', 'user_id');
        $model->user_ids = $old_user_ids;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                $flag = $flag && $model->updateUser($old_user_ids);
                if ($flag) {
                    $transaction->commit();
                    $this->flash("success", Module::t('module', 'Item Updated'));
                    return $this->redirect(['index', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Module::t('module', "Error In Save Info"));
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $user_id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionUpdateUser($user_id)
    {
        $model = $this->findModelUser($user_id);
        $model->setScenario(EmployeeBranchUser::SCENARIO_UPDATE);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson([
                'success' => true,
                'msg' => Module::t('module', 'Item Updated')
            ]);
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_update_user', [
            'model' => $model,
        ]);
    }

    /**
     * @param $user_id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionInsuranceData($user_id)
    {
        $model = $this->findModelUser($user_id);
        $model->setScenario(EmployeeBranchUser::SCENARIO_INSURANCE);
        $request = Yii::$app->request;

        $insuranceData = $model->getInsuranceData();

        $model->employee_address = $insuranceData['employee_address'];
        $model->first_name = $insuranceData['first_name'];
        $model->last_name = $insuranceData['last_name'];
        $model->nationalCode = $insuranceData['nationalCode'];

        if ($request->isPost) {
            $updateAvatar = true;
            $user = Module::getInstance()->user::findOne(Yii::$app->user->getId());
            if ($avatar = UploadedFile::getInstance($model, 'avatar')) {
                $user->scenario = Module::getInstance()->user::SCENARIO_UPDATE_AVATAR;
                $user->avatar = $avatar;
                $updateAvatar = $user->save();
            }

            $model->children = EmployeeChild::createMultiple(EmployeeChild::class);
            EmployeeChild::loadMultiple($model->children, $request->post());
            $valid = EmployeeChild::validateMultiple($model->children);

            $model->experiences = EmployeeExperience::createMultiple(EmployeeExperience::class);
            EmployeeExperience::loadMultiple($model->experiences, $request->post());
            $valid = $valid && EmployeeExperience::validateMultiple($model->experiences);

            $updateProfile = $valid && $updateAvatar && $model->load($request->post());

            if ($updateProfile) {
                $model->pending_data = null;
                $updateProfile = $model->save();
            }

            if (!$updateAvatar) {
                $model->addError('avatar', $user->getFirstError('avatar') ?: Module::t('module', 'Error In Save Information, Please Try Again'));
            }

            if ($updateProfile) {
                return $this->asJson([
                    'success' => true,
                    'msg' => Module::t('module', 'Item Updated')
                ]);
            }
        }

        if ($request->isGet) {
            $model->children = $model->getChildrenWithPending();
            $model->experiences = $model->getExperiencesWithPending();
        }

        if (!count($model->children)) {
            $model->children = [new EmployeeChild(['isNewRecord' => true])];
        }

        if (!count($model->experiences)) {
            $model->experiences = [new EmployeeExperience(['isNewRecord' => true])];
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('@hesabro/hris/views/employee-profile/update', [
            'model' => $model
        ]);
    }

    public function actionRejectUpdate($user_id)
    {
        $model = $this->findModelUser($user_id);
        $model->setScenario(EmployeeBranchUser::SCENARIO_REJECT_UPDATE);
        $rejected = false;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->pending_data = null;
            $model->reject_update_description_seen = false;
            $rejected = $model->save(false);
        }

        return $this->asJson([
            'success' => $rejected,
            'msg' => Module::t('module', $rejected ? 'Item Rejected' : 'Can Not Update')
        ]);
    }


    /**
     * @param $id
     * @return array|string|Response
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionSetEndWork($id)
    {
        $model = $this->findModelUser( $id);
        $model->setScenario(EmployeeBranchUser::SCENARIO_SET_END_WORK);
        if (!$model->canSetEndWork()) {
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation."));
        }
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if (SalaryPeriodItems::find()
                        ->andWhere(['user_id' => $model->user_id])
                        ->bySalary()
                        ->untilYear(strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($model->end_work)))->limit(1)->one() !== null) {
                    $flag = $flag && $model->saveDocumentEndWork();
                }

                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Updated")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $result['msg'] = $e->getMessage();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
            }
            return $this->asJson($result);
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_set-end-work', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionReturnEndWork($id)
    {
        $model = $this->findModelUser( $id);

        if ($model->canReturnEndWork()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->end_work = '';
                $flag = $model->save(false);
                $flag = $flag && $model->deleteDocumentEndWork();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Updated")
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
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "It is not possible to perform this operation")
            ];
        }
        return $this->asJson($result);
    }

    /**
     * @param $id
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionStartWorkAgain($id)
    {
        $model = $this->findModelUser($id);

        if ($model->canStartWorkAgain()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $history = new EmployeeHistory();
                $history->start_work = $model->start_work;
                $history->end_work = $model->end_work;
                if (($document = $model->getDocumentEndWork()) !== null) {
                    $history->document_id_end_work = $document->id;
                }

                if ($model->history) {
                    $model->history = [];
                }
                $model->history[] = $history;
                $model->start_work = Yii::$app->jdf->jdate("Y/m/01");
                $model->end_work = '';
                $model->updateSettlements(false);
                $flag = $model->save(false);
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Updated")
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
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "It is not possible to perform this operation")
            ];
        }
        return $this->asJson($result);
    }

    /**
     * Deletes an existing EmployeeBranch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->softDelete()) {
            $this->flash('success', Module::t('module', "Item Deleted"));
        }
        return $this->redirect(['index']);
    }

    /**
     * Lists all Year models.
     * @return mixed
     */
    public function actionYearSetting()
    {
        $searchModel = new YearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('year-setting', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionUpdateYearSetting($id)
    {
        $model = $this->findModelYear($id);
        $model->setScenario(Year::SCENARIO_UPDATE_SALARY_JSON);
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Created")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            }
            return $this->asJson($result);
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_update-salary', [
            'model' => $model,
        ]);
    }

    public function actionChangeBranch($user_id)
    {
        $model = $this->findModelUser($user_id);
        $model->setScenario(EmployeeBranchUser::SCENARIO_CHANGE_BRANCH);
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
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
            }
            return $this->asJson($result);
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_change-branch', [
            'model' => $model,
        ]);
    }

    public function actionUserDetail($id)
    {
        $model = Module::getInstance()->user::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->renderAjax('_user-detail', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Year model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Year the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelYear($id)
    {
        if (($model = Year::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    /**
     * Finds the EmployeeBranch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeBranch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeeBranch::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the EmployeeBranchUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $userId
     * @return EmployeeBranchUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelUser($userId)
    {
        if (($model = EmployeeBranchUser::find()->andWhere(['user_id' => $userId])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}