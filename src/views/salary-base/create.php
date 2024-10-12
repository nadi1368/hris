<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryBase */

$this->title = Module::t('module', 'Create Salary Base');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-base-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
