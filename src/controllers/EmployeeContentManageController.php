<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\EmployeeContent;
use hesabro\hris\models\EmployeeContentClause;
use hesabro\hris\models\EmployeeContentSearch;
use himiklab\sortablegrid\SortableGridAction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class EmployeeContentManageController extends Controller
{
    use AjaxValidationTrait;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'upload-image' => ['POST'],
//                    'clauses' => ['POST']
                ],
            ],
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['hris/employee-content-manage/index'],
//                        'actions' => ['index', 'clauses']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['hris/employee-content-manage/create'],
//                        'actions' => ['create', 'sort', 'upload-image']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['hris/employee-content-manage/update'],
//                        'actions' => ['update', 'sort', 'upload-image', 'remove-attachment']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['hris/employee-content-manage/delete'],
//                        'actions' => ['delete']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['hris/employee-content-manage/view'],
//                        'actions' => ['view']
//                    ]
//                ]
//            ]
        ];
    }

    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::class,
                'modelName' => EmployeeContent::class,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'upload-image') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Faq models.
     * @return mixed
     */
    public function actionIndex($type)
    {
        EmployeeContent::$templateReplacement = false;
        $searchModel = new EmployeeContentSearch(['type' => $type]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'title' => EmployeeContent::itemAlias('Type', $type),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type,
            'isTypeSet' => (bool) $type
        ]);
    }

    public function actionView($id)
    {
        EmployeeContent::$templateReplacement = false;
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Faq model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        EmployeeContent::$templateReplacement = false;
        $model = new EmployeeContent(['scenario' => EmployeeContent::SCENARIO_CREATE]);
        $model->clauses = [new EmployeeContentClause()];

        if (!$model->canCreate()) {
            $this->flash('danger', Yii::t("app", "Can Not Create"));
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->clauses = EmployeeContentClause::createMultiple(EmployeeContentClause::class);
            EmployeeContentClause::loadMultiple($model->clauses, Yii::$app->request->post());
            EmployeeContentClause::validateMultiple($model->clauses);

            if ($model->save()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true, 'msg' => Yii::t("app", 'Item Created')];
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('create', [
            'model' => $model,
            'type' => $type,
            'isTypeSet' => EmployeeContent::validateType($type)
        ]);
    }

    /**
     * Updates an existing Faq model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $type)
    {
        EmployeeContent::$templateReplacement = false;
        $model = $this->findModel($id);

        if (!$model->clauses || count($model->clauses) < 1) {
            $model->clauses = [new EmployeeContentClause(['content' => $model->description])];
        }

        if (!$model->canUpdate()) {
            $this->flash('danger', Yii::t("app", "Can Not Update"));
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->clauses = EmployeeContentClause::createMultiple(EmployeeContentClause::class);
            EmployeeContentClause::loadMultiple($model->clauses, Yii::$app->request->post());
            EmployeeContentClause::validateMultiple($model->clauses);

            if ($model->save()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true, 'msg' => Yii::t("app", 'Item Updated')];
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('update', [
            'model' => $model,
            'type' => $type,
            'isTypeSet' => EmployeeContent::validateType($type)
        ]);
    }

    /**
     * Deletes an existing Faq model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->softDelete()) {
            $this->flash('success', Yii::t("app", "Item Deleted"));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Upload ckeditor image
     * @return mixed
     */
    public function actionUploadImage($id)
    {
        $model = $this->findModel($id);
        $funcNum = Yii::$app->request->get('CKEditorFuncNum');

        $model->images = UploadedFile::getInstanceByName('upload');

        if ($saved = $model->save(false) && $url = $model->getStorageFile('images')->orderBy(['created_at' => SORT_DESC])->one()?->getFileUrl('images')) {
            $script = "window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '');";
        } else {
            $errorMessage = 'Could not upload file.';
            $script = "window.parent.CKEDITOR.tools.callFunction($funcNum, '', '$errorMessage');";
        }

        // Return the script to be executed by CKEditor
        return "<script>$script</script>";
    }

    /**
     * Remove faq attachment
     * @return mixed
     */
    public function actionRemoveAttachment($id)
    {
        $model = $this->findModel($id);

        $model->getStorageFile('attachment')->one()->softDelete();

        return $this->redirect(['index']);
    }

    /**
     * Get faq clauses
     * @return mixed
     */
    public function actionClauses($selected = 0)
    {
        $output = '';

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $employeeContentId = $parents[0];
                $employeeContent = $this->findModel($employeeContentId);

                $output = array_map(fn($clause) => [
                    'id' => $clause['id'],
                    'name' => strip_tags($clause['content']),
                ], $employeeContent->clauses);
            }
        }

        return $this->asJson(['output' => $output, 'selected' => $selected]);
    }

    /**
     * Finds the Faq model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeContent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeeContent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}