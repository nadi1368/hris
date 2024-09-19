<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriodItems */

$this->title = Yii::t('app', 'Create Salary Period Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Salary Period Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-period-items-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
