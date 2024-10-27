<?php
use hesabro\hris\models\EmployeeBranchUser;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $model EmployeeBranchUser */

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
		<?= $this->render('_view-user', [
			'model' => $model,
		]) ?>
		<?php Pjax::end() ?>
	</div>
</div>
