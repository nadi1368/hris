<?php

use hesabro\hris\models\RequestLeaveSearch;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel RequestLeaveSearch */

$this->title = Module::t('module', 'Request Leave Admin');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@hesabro/hris/views/layouts/_requests_tabs.php', [
    'content' => $this->render('_admin', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
