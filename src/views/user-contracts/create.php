<?php


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContracts */

use hesabro\hris\Module;

$this->title = Module::t('module', 'Create User Contracts');
$this->params['breadcrumbs'][] = ['label' => $model->user->fullName, 'url' => ['user-contracts/employee-contracts', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contracts-create card">
	<?= $this->render('_form', [
		'model' => $model,
		'modelUser' => $modelUser,
	]) ?>
</div>
