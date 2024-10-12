<?php

use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryInsurance */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="salary-insurance-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
