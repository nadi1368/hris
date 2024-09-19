<?php


/* @var $this View */
/* @var $model EmployeeBranchUser */


use hesabro\hris\models\EmployeeBranchUser;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = $model->user->fullName;
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="card">
	<div class="card-header">
		<?= $this->render('_view_user_nav', [
			'model' => $model,
		]) ?>
	</div>

	<div class="card-body">
		<?php Pjax::begin(['id' => 'view-user-info-pjax']) ?>
		<?= $this->render('_employee_info', [
			'model' => $model,
		]) ?>
		<?php Pjax::end() ?>
	</div>
</div>
