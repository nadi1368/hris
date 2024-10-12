<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriodItems */

$this->title = Module::t('module', 'Create Salary Period Items');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Period Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-period-items-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
