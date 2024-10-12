<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module','Employee Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-branch-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'title',
            'manager',
            'status',
            'creator_id',
            'update_id',
            'created',
            'changed',
		],
	]) ?>
	</div>
	<div class="card-footer">
		<?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Delete', ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
		'confirm' => 'Are you sure you want to delete this item?',
		'method' => 'post',
		],
		]) ?>
	</div>
</div>
