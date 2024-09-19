<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\RateOfYearSalary */

$this->title = Yii::t('app', 'Create Rate Of Year Salary');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rate Of Year Salaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rate-of-year-salary-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
