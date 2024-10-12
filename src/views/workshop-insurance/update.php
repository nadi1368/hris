<?php

use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\WorkshopInsurance */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Workshop Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="workshop-insurance-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
