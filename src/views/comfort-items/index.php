<?php

use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\ComfortItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@backend/modules/employee/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('@backend/modules/employee/views/comfort-items/_index.php', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
