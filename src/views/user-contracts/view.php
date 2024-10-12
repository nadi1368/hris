<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContracts */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'User Contracts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contracts-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'contract_id',
            'user_id',
            'start_date',
            'end_date',
            'month',
            'variables',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
		],
	]) ?>
	</div>
	<div class="card-footer">
		<?php // Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
		'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
		'method' => 'post',
		],
		]) ?>
	</div>
</div>
