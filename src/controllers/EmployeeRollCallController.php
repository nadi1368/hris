<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeRollCallSearch;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class EmployeeRollCallController extends Controller
{
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
     * Lists all EmployeeRollCall models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeRollCallSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
