<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\ComfortItemsSearch;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use hesabro\hris\models\Comfort;
use hesabro\hris\models\ComfortSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ComfortController implements the CRUD actions for Comfort model.
 */
class EmployeeComfortController extends Controller
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
                            'roles' => Module::getInstance()->employeeRole,
                        ],
                    ]
            ]
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $this->layout = Module::getInstance()->layoutPanel;
        $employee = $this->findModelEmployee(Yii::$app->user->id);
        $searchModel = new ComfortSearch();
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams, $employee);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionItems()
    {
        $this->layout = Module::getInstance()->layoutPanel;
        $searchModel = new ComfortItemsSearch();
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams);

        return $this->render('items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $employee = $this->findModelEmployee(Yii::$app->user->id);
        return $this->renderAjax('view', [
            'model' => $this->findModel($id, $employee),
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewItems($id): string
    {
        $employee = $this->findModelEmployee(Yii::$app->user->id);
        return $this->renderAjax('_view-items', [
            'model' => $this->findModelItems($id, $employee->user_id),
        ]);
    }


    /**
     * @param int $comfort_id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionCreate(int $comfort_id)
    {
        $employee = $this->findModelEmployee(Yii::$app->user->id);
        $comfort = $this->findModel($comfort_id, $employee);
        $model = new ComfortItems([
            'comfort_id' => $comfort->id,
            'user_id' => $employee->user_id,
            'employee' => $employee
        ]);
        $model->setScenarioByComfort(ComfortItems::SCENARIO_CREATE, $comfort);

        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];

        if ($model->load(Yii::$app->request->post())) {
            if ($comfort->document_required) {
                $model->file_name = UploadedFile::getInstance($model, 'file_name');
            }
            if ($model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $comfort->document_required && $model->attach = uniqid() . '.' . $model->file_name->extension;
                    $flag = $model->save(false);
                    $flag = $comfort->document_required ? ($flag && $model->uploadFileToCdn('file_name', $model->attach)) : true;
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
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $result;
            }
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
            'comfort' => $comfort,
        ]);
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): array
    {
        $employee = $this->findModelEmployee(Yii::$app->user->id);
        $model = $this->findModelItems($id, $employee->user_id);
        if ($model->canDelete()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->softDelete();
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
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Finds the Comfort model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @param EmployeeBranchUser $employee
     * @return Comfort the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, EmployeeBranchUser $employee): Comfort
    {
        if (($model = Comfort::find()->andWhere(['id' => $id])->canShow($employee)->limit(1)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    /**
     * Finds the ComfortItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @param int $userId
     * @return ComfortItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelItems(int $id, int $userId): ComfortItems
    {
        if (($model = ComfortItems::find()->andWhere(['id' => $id])->byUser($userId)->limit(1)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    /**
     * Finds the EmployeeBranchUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return EmployeeBranchUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelEmployee(int $id): EmployeeBranchUser
    {
        if (($model = EmployeeBranchUser::find()->byUserId($id)->limit(1)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
