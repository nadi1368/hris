<?php

use yii\helpers\Html;
use hesabro\hris\models\WorkshopInsurance;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\SalaryPeriodSearch */
?>
<ul class="nav nav-tabs nav-fill  bg-white pt-3">
    <?php foreach (WorkshopInsurance::itemAlias('List') as $key => $title): ?>
        <li class="nav-item">
            <?= Html::a($title, ['index', 'SalaryPeriodSearch[workshop_id]' => $key], ['class' => $key == $searchModel->workshop_id ? 'nav-link active' : 'nav-link']); ?>
        </li>
    <?php endforeach; ?>
</ul>