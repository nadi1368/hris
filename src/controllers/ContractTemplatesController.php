<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\ContractClausesModel;
use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\ContractTemplatesSearch;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ContractTemplatesController implements the CRUD actions for ContractTemplates model.
 */
class ContractTemplatesController extends Controller
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
                            'roles' => ['ContractTemplates/index'],
                            'actions' => ['index', 'json-export', 'json-import']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['ContractTemplates/create'],
                            'actions' => ['create']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['ContractTemplates/update'],
                            'actions' => ['update']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['ContractTemplates/delete'],
                            'actions' => ['delete']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['ContractTemplates/view'],
                            'actions' => ['view']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all ContractTemplates models.
     * @return mixed
     */
    public function actionIndex($type = null)
    {
        $type = $type ?: ContractTemplates::TYPE_CONTRACT;
        $searchModel = new ContractTemplatesSearch([
            'type' => $type
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single ContractTemplates model.
     * @param int $id شناسه
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
     * Creates a new ContractTemplates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($copy_contract_id = null, $type = null)
    {
        $type = $type ?: ContractTemplates::TYPE_CONTRACT;
        $model = new ContractTemplates();
        $oldContract = $copy_contract_id ? $this->findModel($copy_contract_id) : null;
        $model->type = $oldContract?->type ?: $type;

        if ($model->type != ContractTemplates::TYPE_LETTER) {
            $model->clausesModels = [new ContractClausesModel()];
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->type != ContractTemplates::TYPE_LETTER) {
                $model->clausesModels = ContractClausesModel::createMultipleWithScenario(ContractClausesModel::class);
                ContractClausesModel::loadMultiple($model->clausesModels, Yii::$app->request->post());
                ContractClausesModel::validateMultiple($model->clausesModels);
            }

            if($model->save()) {
                return $this->redirect(['index', 'type' => $model->type]);
            }
        } else if ($oldContract) {
            $model->attributes = $oldContract->attributes;
            $model->clausesModels = $oldContract->clausesModels;
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing ContractTemplates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id شناسه
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            $this->flash('danger', Yii::t("app", "Can Not Update"));
            return $this->redirect(['index', 'type' => $model->type ?: ContractTemplates::TYPE_CONTRACT]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->clausesModels = ContractClausesModel::createMultipleWithScenario(ContractClausesModel::class);
            ContractClausesModel::loadMultiple($model->clausesModels, Yii::$app->request->post());
            ContractClausesModel::validateMultiple($model->clausesModels);

            if ($model->save()) {
                return $this->redirect(['index', 'type' => $model->type]);
            }
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing ContractTemplates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id شناسه
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->softDelete()) {
            $this->flash('success', Yii::t("app", "Item Deleted"));
        }
        return $this->redirect(['index', 'type' => $model->type ?: ContractTemplates::TYPE_CONTRACT]);
    }


    public function actionJsonExport($contract_id)
    {
        $model = $this->findModel($contract_id);
        $dataArray = $model->toArray([
            'title',
            'type',
            'description',
            'clauses',
            'variables',
            'signatures',
        ]);

        // Encode the array to JSON
        $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);

        // Set response headers to force download
        \Yii::$app->response->format = Response::FORMAT_RAW;
        \Yii::$app->response->headers->add('Content-Type', 'application/json');
        \Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="model_' . $contract_id . '.json"');

        // Send the JSON data
        return $jsonData;
    }

    public function actionJsonImport()
    {
        $result = [
            'success' => false,
            'msg' => Yii::t('app', 'Error In Save Info'),
        ];

        $model = new ContractTemplates();

        if(Yii::$app->request->isPost) {
            $model->json_file = UploadedFile::getInstance($model, 'json_file');
            $data = json_decode(file_get_contents($model->json_file->tempName), true);

            $model->load($data, '');

            if($model->save()){
                $result = [
                    'success' => true,
                    'msg' => Yii::t("app", "Item Created")
                ];
            } else {
                $result['msg'] = $model->hasErrors() ? Html::errorSummary($model) : $result['msg'];
            }

            return $this->asJson($result);
        }

        return $this->renderAjax('json-import', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ContractTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return ContractTemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContractTemplates::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
