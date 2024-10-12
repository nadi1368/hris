<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryItemsAddition */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Items Additions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-items-addition-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'user_id',
            'kind',
            'type',
            'second',
            'from_date',
            'to_date',
            'description:ntext',
            'status',
            'creator_id',
            'update_id',
            'created',
            'changed',
		],
	]) ?>
	</div>
	<div class="card-footer">
		<?= Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
		'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
		'method' => 'post',
		],
		]) ?>
	</div>
</div>
