<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryInsurance */

$this->title = Module::t('module', 'Create Salary Insurance');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-insurance-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
