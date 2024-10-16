<?php

use hesabro\hris\models\EmployeeRequestSearch;
use hesabro\hris\Module;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var EmployeeRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var View $this
 */

$this->title = Module::t('module', 'Requests');
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile('@hesabro/hris/views/layouts/_requests_tabs.php', [
    'content' => $this->renderFile('@hesabro/hris/views/employee-request/_index.php', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ])
]);
