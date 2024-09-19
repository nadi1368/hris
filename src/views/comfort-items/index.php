<?php

use hesabro\hris\models\ComfortItems;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\ComfortItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@backend/modules/employee/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('@backend/modules/employee/views/comfort-items/_index.php', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
