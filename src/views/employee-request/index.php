<?php

use hesabro\hris\models\EmployeeRequestSearch;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var EmployeeRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var View $this
 */

$this->title = Yii::t('app', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@backend/modules/employee/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('@backend/modules/employee/views/employee-request/_index.php', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
