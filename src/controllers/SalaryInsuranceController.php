<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\traits\AjaxValidationTrait;
use Exception;
use hesabro\hris\Module;
use Yii;
use hesabro\hris\models\SalaryInsurance;
use hesabro\hris\models\SalaryInsuranceSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * SalaryInsuranceController implements the CRUD actions for SalaryInsurance model.
 */
class SalaryInsuranceController extends Controller
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
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['SalaryPeriod/index', 'superadmin'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all SalaryInsurance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalaryInsuranceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalaryInsurance model.
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
     * @return array|string
     */
    public function actionCreate()
    {
        $model = new SalaryInsurance();
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
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return array|string
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation"));
        }
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
                        'msg' => Module::t('module', "Item Updated")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
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

    public function actionList($q = null)
    {
        $out = [];
        if (!is_null($q)) {
            $query = SalaryInsurance::find();
            $searchKeys = explode(' ', $q ,3);
            $conditions = ['or'];
            foreach ($searchKeys as $searchKey) {
                if ($searchKey) {
                    $conditions[] = ['like', 'code', $searchKey];
                    $conditions[] = ['like', 'group', $searchKey];
                }
            }
            $query->andWhere($conditions);

            $k = 0;
            foreach ($query->all() as $item) {
                //$stock=$item->getTotalStock(false, true);
                $out['results'][$k]['id'] = $item->id;
                $out['results'][$k]['text'] = "$item->group ($item->code)";
                $k++;
            }
        }
        return $this->asJson($out);
    }

    /**
     * Finds the SalaryInsurance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalaryInsurance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalaryInsurance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
