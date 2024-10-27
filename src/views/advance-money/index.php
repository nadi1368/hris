<?php

use hesabro\hris\models\AdvanceMoneySearch;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $searchModel AdvanceMoneySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Module::t('module', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@hesabro/hris/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('@hesabro/hris/views/advance-money/_index.php', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
