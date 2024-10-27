<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\AdvanceMoneySearch;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AdvanceMoneyController implements the CRUD actions for AdvanceMoney model.
 */
class EmployeeAdvanceMoneyController extends Controller
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
     * Lists all AdvanceMoney models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdvanceMoneySearch();
        $searchModel->user_id = Yii::$app->user->id;
        $dataProvider = $searchModel->searchMy(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionCreate()
    {
        $model = new AdvanceMoney([
            'scenario' => AdvanceMoney::SCENARIO_CREATE,
            'user_id' => Yii::$app->user->id
        ]);
		$model->iban = $model->employee->shaba;

        if(!$model->canCreate())
        {
            throw new NotFoundHttpException($model->error_msg);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'msg' => Yii::t('app', 'Item Created')
            ];
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing AdvanceMoney model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $result = ['status' => true];

        if ($model->canDelete() && $model->softDelete()) {
            $result = [
                'success' => true,
                'msg' => Yii::t("app", "Item Deleted")
            ];
        } else {
            $result = [
                'success' => false,
                'msg' => Yii::t("app", "Error In Save Info")
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Finds the AdvanceMoney model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdvanceMoney the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdvanceMoney::findOne($id)) !== null && $model->user_id == Yii::$app->user->id) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
