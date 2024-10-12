<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\UserContracts;
use hesabro\hris\models\UserContractsSearch;
use hesabro\hris\models\UserContractsShelves;
use hesabro\hris\models\UserContractsShelvesSearch;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserContractsShelvesController implements the CRUD actions for UserContractsShelves model.
 */
class UserContractsShelvesController extends Controller
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
                        'roles' => ['UserContractsShelves/index'],
                        'actions' => ['index']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['UserContractsShelves/create'],
                        'actions' => ['create']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['UserContractsShelves/update'],
                        'actions' => ['update']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['UserContractsShelves/delete'],
                        'actions' => ['delete']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['UserContractsShelves/view'],
                        'actions' => ['view']
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all UserContractsShelves models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserContractsShelvesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserContractsShelves model.
     * @param int $id شناسه
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);

		$contractsSearchModel = new UserContractsSearch(['shelf_id' => $model->id, 'status' => UserContracts::STATUS_CONFIRM]);
		$contractsDataProvider = $contractsSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $model,
			'contractsSearchModel' => $contractsSearchModel,
			'contractsDataProvider' => $contractsDataProvider,
        ]);
    }

    /**
     * Creates a new UserContractsShelves model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserContractsShelves();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = [
                'success' => true,
                'msg' => Module::t('module', 'Item Created')
            ];

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserContractsShelves model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id شناسه
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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = [
                'success' => true,
                'msg' => Module::t('module', 'Item Updated')
            ];

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('update', [
        'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserContractsShelves model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id شناسه
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->softDelete()) {
            $this->flash('success', Module::t('module', "Item Deleted"));
        } else {
			$this->flash('error', $model->err_msg ?: Module::t('module','Error In Save Info'));
		}
        return $this->redirect(['index']);
    }

    /**
     * Finds the UserContractsShelves model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return UserContractsShelves the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserContractsShelves::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
