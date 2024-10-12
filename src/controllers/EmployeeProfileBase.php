<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeRollCallSearch;
use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\models\SalaryPeriodItemsSearch;
use hesabro\hris\models\UserContractsSearch;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;


class EmployeeProfileBase extends Controller
{
    use AjaxValidationTrait;
    public $layout = '@backend/modules/employee/views/layouts/panel.php';

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
                            'roles' => ['employee'],
                        ],
                    ]
            ]
        ];
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSalaryPeriod()
    {
        $searchModel = new SalaryPeriodItemsSearch(['user_id' => Yii::$app->user->id]);
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams);
        $dataProvider->query
            ->joinWith('period')
            ->andWhere([SalaryPeriod::tableName() . '.status' => SalaryPeriod::STATUS_PAYMENT])
            ->andWhere([SalaryPeriodItems::tableName() . '.can_payment' => Yii::$app->helper::CHECKED])
            ->andWhere(['user_id' => Yii::$app->user->id]);
        return $this->render('salary-period', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrintSingleItem($id): string
    {
        $this->layout = '@backend/views/layouts/print-bootstrap';
        $salaryPeriodItem = $this->findModelSalaryItem($id);
        if (!$salaryPeriodItem->canPrint()) {
            throw new ForbiddenHttpException('امکان چاپ وجود ندارد');
        }
        return $this->render('print-single-item', [
            'model' => $salaryPeriodItem,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewSingleItem($id): string
    {
        $salaryPeriodItem = $this->findModelSalaryItem($id);
        if (!$salaryPeriodItem->canPrint()) {
            throw new ForbiddenHttpException('امکان مشاهده وجود ندارد');
        }
        return $this->render('view-single-item', [
            'model' => $salaryPeriodItem,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRollCall()
    {
        $searchModel = new EmployeeRollCallSearch();
        $startAndEndOfCurrentMonth = Yii::$app->jdf::getStartAndEndOfCurrentMonth();
        $start_month = Yii::$app->jdf->jdate('Y/m/d', $startAndEndOfCurrentMonth[0]);
        $end_month = Yii::$app->jdf->jdate('Y/m/d', $startAndEndOfCurrentMonth[1]);
        $searchModel->fromDate = $start_month;
        $searchModel->toDate = $end_month;
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams, Yii::$app->user->id);
        return $this->render('roll-call', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionContract()
    {
        $id = Yii::$app->user->id;

        $searchModel = new UserContractsSearch(['user_id' => $id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('contract', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return object the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelUser($id)
    {
        if (($model = Module::getInstance()->user::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }


    /**
     * @param $id
     * @return SalaryPeriodItems|null
     * @throws NotFoundHttpException
     */
    protected function findModelSalaryItem($id): ?SalaryPeriodItems
    {
        if (($model = SalaryPeriodItems::findOne($id)) !== null && $model->user_id == Yii::$app->user->id) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }


    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
