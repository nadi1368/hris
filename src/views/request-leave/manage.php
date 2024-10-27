<?php
/* @var $this yii\web\View */
/* @var $searchModel backend\models\RequestLeaveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use hesabro\hris\Module;

$this->title = Module::t('module', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@hesabro/hris/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('_manage', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
