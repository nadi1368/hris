<?php

use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ContractTemplates */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Contract Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="contract-templates-update card">
	<?= $this->render('_form', [
		'model' => $model
	]) ?>
</div>
