<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\InternalNumber;
use hesabro\hris\models\InternalNumberSearch;
use hesabro\hris\Module;
use himiklab\sortablegrid\SortableGridAction;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * InternalNumberController implements the CRUD actions for InternalNumber model.
 */
class InternalNumberController extends Controller
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
                            'roles' => ['InternalNumber/index', 'superadmin'],
                            'actions' => ['index', 'json-export', 'json-export-all', 'json-import','delete-all']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['InternalNumber/actions', 'superadmin'],
                            'actions' => ['create', 'update', 'sort', 'delete']
                        ],
                        [
                            'allow' => true,
                            'roles' =>  Module::getInstance()->employeeRole,
                            'actions' => ['public', 'resort']
                        ],
                    ]
            ]
        ];
    }

    public function actions(): array
    {
        return [
            'sort' => [
                'class' => SortableGridAction::class,
                'modelName' => InternalNumber::class,
            ],
        ];
    }


    /**
     * Set Sort existing data
     */
    public function actionResort()
    {
        $sort = 1;
        $numbers = InternalNumber::find()->all();

        foreach($numbers as $number) {
            $number->sort = $sort++;
            $number->save();
        }

        return $this->asJson('DONE :)');
    }


    /**
     * Lists all InternalNumber models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InternalNumberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all InternalNumber models.
     * @return mixed
     */
    public function actionPublic()
    {
        $this->layout = Module::getInstance()->layoutPanel;

        $searchModel = new InternalNumberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('public', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new InternalNumber model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InternalNumber();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $transaction->commit();
                    return $this->asJson([
                        'success' => true, 'msg' =>
                            'اطلاعات با موفقیت ثبت شد.'
                    ]);
                } else {
                    $transaction->rollBack();
                    return $this->asJson([
                        'success' => false,
                        'msg' => 'خطا در ثبت اطلاعات.'
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson([
                    'success' => false,
                    'msg' => 'خطا در ثبت اطلاعات.'
                ]);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', ['model' => $model]);
    }

    /**
     * Updates an existing InternalNumber model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            return $this->asJson([
                'success' => false,
                'msg' => Module::t('module', "Can Not Update")
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $transaction->commit();
                    return $this->asJson([
                        'success' => true, 'msg' =>
                            'اطلاعات با موفقیت ثبت شد.'
                    ]);
                } else {
                    $transaction->rollBack();
                    return $this->asJson([
                        'success' => false,
                        'msg' => 'خطا در ثبت اطلاعات.'
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->asJson([
                    'success' => false,
                    'msg' => 'خطا در ثبت اطلاعات.'
                ]);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', ['model' => $model]);
    }

    /**
     * Export an existing InternalNumber model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionJsonExport($id)
    {
        $model = $this->findModel($id);
        $dataArray = [
            $model->toArray([
                'name',
                'number',
                'job_position',
            ])
        ];

        // Encode the array to JSON
        $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);

        // Set response headers to force download
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/json');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="model_' . $id . '.json"');

        // Send the JSON data
        return $jsonData;
    }

    /**
     * Export all InternalNumber models
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionJsonExportAll()
    {
        $models = InternalNumber::find()->orderBy('sort')->all();
        $dataArray = array_map(fn($model) => $model->toArray([
            'name',
            'number',
            'job_position',
            'sort',
        ]), $models);

        // Encode the array to JSON
        $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);

        // Set response headers to force download
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/json');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="internal_numbers.json"');

        // Send the JSON data
        return $jsonData;
    }

    /**
     * Import InternalNumber models from json.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionJsonImport()
    {
        $result = [
            'success' => false,
            'msg' => Module::t('module', 'Error In Save Info'),
        ];

        $model = new InternalNumber();

        if (Yii::$app->request->isPost) {
            $model->json_file = UploadedFile::getInstance($model, 'json_file');
            $data = json_decode(file_get_contents($model->json_file->tempName), true);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = true;
                foreach ($data as $datum) {
                    $newModel = new InternalNumber();
                    $newModel->load($datum, '');
                    $flag = $flag && $newModel->save();
                    if(!$flag){
                        $result['msg'] = $newModel->hasErrors() ? Html::errorSummary($newModel) : $result['msg'];
                        break;
                    }
                }
                if($flag){
                    $transaction->commit();
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Created")
                    ];
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            return $this->asJson($result);
        }

        return $this->renderAjax('json-import', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InternalNumber model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->delete()) {
                $transaction->commit();
                $this->flash('success', Module::t('module', 'Item Deleted'));
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    public function actionDeleteAll()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            InternalNumber::deleteAll();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the InternalNumber model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InternalNumber the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InternalNumber::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
