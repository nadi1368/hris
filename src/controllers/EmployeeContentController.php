<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeContent;
use hesabro\hris\models\EmployeeContentSearch;
use hesabro\hris\Module;
use Yii;
use yii\web\Controller;

class EmployeeContentController extends Controller
{
    public function init()
    {
        parent::init();
        $this->layout = Module::getInstance()->layoutPanel;
    }

    public function actionIndex($type, $faq_id = null, $clause_id = null)
    {
        $searchModel = new EmployeeContentSearch(['type' => $type, 'id' => $faq_id]);
        $dataProvider = $searchModel->searchForEmployee(Yii::$app->request->queryParams, false);

        return $this->render('public', [
            'title' => EmployeeContent::itemAlias('Type', $type),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'faqId' => $faq_id,
            'clauseId' => $clause_id,
        ]);
    }
}
