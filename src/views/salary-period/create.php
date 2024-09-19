<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */

$this->title = Yii::t('app', 'Create Salary Period');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Salary Periods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-period-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
