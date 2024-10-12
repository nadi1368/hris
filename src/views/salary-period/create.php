<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */

$this->title = Module::t('module', 'Create Salary Period');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Periods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-period-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
