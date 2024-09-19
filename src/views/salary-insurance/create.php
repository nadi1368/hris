<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryInsurance */

$this->title = Yii::t('app', 'Create Salary Insurance');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Salary Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-insurance-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
