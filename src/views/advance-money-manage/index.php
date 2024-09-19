<?php

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdvanceMoneySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@backend/modules/employee/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('@backend/modules/employee/views/advance-money-manage/_index.php', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
