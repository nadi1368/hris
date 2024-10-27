<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\RequestLeave;
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\hris\models\RejectForm;
use hesabro\hris\Module;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii2tech\spreadsheet\Spreadsheet;

/**
 * RequestLeaveController implements the CRUD actions for RequestLeave model.
 */
class EmployeeRequestLeaveController extends Controller
{
    use AjaxValidationTrait;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->layout = Module::getInstance()->layoutPanel;
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
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => Module::getInstance()->employeeRole,
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all RequestLeave models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RequestLeaveSearch();
        $dataProvider = $searchModel->searchMy(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function exportExcel($data_provider)
    {
        $exporter = new Spreadsheet([
            'dataProvider' => $data_provider,
            'columns' => [
                [
                    'attribute' => 'user_id',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->user->fullName;
                    }
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        return RequestLeave::itemAlias('Types', $model->type);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return RequestLeave::itemAlias('Status', $model->status);
                    },
                    'format' => 'raw'
                ],
                'description:ntext',
                [
                    'attribute' => 'from_date',
                    'value' => function ($model) {
                        return in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("Y/m/d", $model->from_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->from_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'to_date',
                    'value' => function ($model) {
                        return in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("Y/m/d", $model->to_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->to_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'range',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDuration($model->to_date - $model->from_date, '  و ');
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return Yii::$app->jdate->date("Y/m/d  H:i", $model->created);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'creator_id',
                    'value' => function ($model) {
                        return $model->creator->fullName;
                    },
                    'format' => 'raw'
                ],
            ]
        ]);

        return $exporter->send(time() . '-request.xls');
    }

    /**
     * Displays a single RequestLeave model.
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

    public function actionCreate()
    {
        $model = new RequestLeave([
            'scenario' => RequestLeave::SCENARIO_CREATE,
            'user_id' => Yii::$app->user->id,
        ]);

        $model->beforeCreate();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $transaction->commit();
                        $result = [
                            'success' => true,
                            'msg' => Yii::t("app", 'Item Created')
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
        }

        $this->performAjaxValidation($model);

        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    public function actionCreateDaily()
    {
        $model = new RequestLeave([
            'scenario' => RequestLeave::SCENARIO_CREATE,
            'user_id' => Yii::$app->user->id,
        ]);

        $model->beforeCreate();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $transaction->commit();
                        $result = [
                            'success' => true,
                            'msg' => Yii::t("app", 'Item Created')
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
        }

        $this->performAjaxValidation($model);

        return $this->renderAjax('_form_daily', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RequestLeave model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            $this->flash('danger', Yii::t("app", "Can Not Update,Your Request confirmed or you can update only the day you requested"));
            return $this->redirect(['index']);
        }
        $model->setScenario(RequestLeave::SCENARIO_UPDATE);
        $model->range = Yii::$app->jdate->date("Y/m/d H:i:s", $model->from_date) . ' - ' . Yii::$app->jdate->date("Y/m/d H:i:s", $model->to_date);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $transaction->commit();
                        $result = [
                            'success' => true,
                            'msg' => Yii::t("app", 'Item Updated')
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
        }

        $this->performAjaxValidation($model);

        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    public function actionUpdateDaily($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            $this->flash('danger', Yii::t("app", "Can Not Update,Your Request confirmed or you can update only the day you requested"));
            return $this->redirect(['index']);
        }
        $model->setScenario(RequestLeave::SCENARIO_UPDATE);
        $model->range = Yii::$app->jdate->date("Y/m/d H:i:s", $model->from_date) . ' - ' . Yii::$app->jdate->date("Y/m/d H:i:s", $model->to_date);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $transaction->commit();
                        $result = [
                            'success' => true,
                            'msg' => Yii::t("app", 'Item Updated')
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
        }

        $this->performAjaxValidation($model);

        return $this->renderAjax('_form_daily', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the RequestLeave model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RequestLeave the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->softDelete();
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
                'message' => Yii::t("app", "It is not possible to perform this operation")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    protected function findModel($id)
    {
        if (($model = RequestLeave::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
