<?php
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $searchModel RequestLeaveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@hesabro/hris/views/layouts/_requests_tabs.php', [
    'content' => $this->render('_manage', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
