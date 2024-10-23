<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeContent;
use hesabro\hris\models\EmployeeContentSearch;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class EmployeeContentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => Module::getInstance()->employeeRole,
                        'actions' => ['index']
                    ]
                ]
            ]
        ];
    }

    public function init()
    {
        parent::init();
        $this->layout = Module::getInstance()->layoutPanel;
    }

    public function actionIndex($type, $faq_id = null, $clause_id = null)
    {
        $searchModel = new EmployeeContentSearch(['type' => $type, 'id' => $faq_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);

        return $this->render('index', [
            'title' => EmployeeContent::itemAlias('Type', $type),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'faqId' => $faq_id,
            'clauseId' => $clause_id,
        ]);
    }
}
