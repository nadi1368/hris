<?php

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsShelves */

use hesabro\hris\Module;

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'User Contracts Shelves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="user-contracts-shelves-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
