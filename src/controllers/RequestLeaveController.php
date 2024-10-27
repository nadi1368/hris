<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\RejectForm;
use hesabro\hris\models\RequestLeave;
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii2tech\spreadsheet\Spreadsheet;

class RequestLeaveController extends Controller
{
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
                    'confirm' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['RequestLeave/admin-branch'],
                        'actions' => ['manage', 'confirm', 'reject', 'sum-merit']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['RequestLeave/admin'],
                        'actions' => ['manage', 'admin', 'confirm', 'reject', 'sum-merit']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['RequestLeave/delete'],
                        'actions' => ['delete']
                    ],
                ]
            ]
        ];
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

    private function findModel($id)
    {
        if (($model = RequestLeave::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Lists all RequestLeave models.
     * @return mixed
     */
    public function actionManage()
    {
        $searchModel = new RequestLeaveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('manage', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all RequestLeave models.
     * @return mixed
     */
    public function actionAdmin($TypeReport = false)
    {
        $searchModel = new RequestLeaveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (isset($TypeReport) && $TypeReport == 'excel') {
            $this->exportExcel($dataProvider);
        }

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionConfirm($id, $status)
    {
        $response = ['success' => false, 'data' => '', 'msg' => Yii::t('app', 'Error In Save Info')];

        $model = $this->findModel($id);
        if ($model->canChangeStatus($status)) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->setScenario(RequestLeave::SCENARIO_CONFIRM);
                $flag = $model->changeStatus($status, '');
                if ($flag) {
                    $transaction->commit();
                    $response['success'] = true;
                    $response['msg'] = Yii::t("app", "Item Confirmed");
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        } else {
            $response['msg'] = Yii::t('app', 'It is not possible to perform this operation');
        }
        return json_encode($response);
    }

    public function actionReject($id, $status)
    {
        $model = $this->findModel($id);
        $form = new RejectForm();
        if ($model->canChangeStatus($status)) {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $model->setScenario(RequestLeave::SCENARIO_REJECT);
                    $flag = $model->changeStatus($status, $form->description);
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

    public function actionSumMerit($id)
    {
        $model = RequestLeave::findOne($id);
        return $this->renderAjax('_sum_merit_leaves', [
            'model' => $model
        ]);
    }

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
}