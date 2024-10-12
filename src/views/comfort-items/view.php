<?php

use hesabro\hris\models\ComfortItems;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortItems */

$this->title = $model->comfort?->title . ' ' . $model->user?->fullName;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Comfort Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comfort-items-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'comfort_id' => [
                'attribute' => $model->getAttributeLabel('comfort_id'),
                'value' => fn(ComfortItems $model) => $model->comfort?->title,
            ],
            'user_id' => [
                'attribute' => $model->getAttributeLabel('user_id'),
                'value' => fn(ComfortItems $model) => $model->user?->fullName,
            ],
            'amount' => [
                'attribute' => $model->getAttributeLabel('amount'),
                'value' => fn(ComfortItems $model) => $model->amount ? number_format($model->amount) : null,
            ],
            'attach',
            'description:ntext',
            'status' => [
                'attribute' => $model->getAttributeLabel('status'),
                'value' => fn(ComfortItems $model) => $model->status ? ComfortItems::itemAlias('Status', $model->status) : null,
            ],
            'created' => [
                'attribute' => $model->getAttributeLabel('created'),
                'value' => fn(ComfortItems $model) => $model->created ? Yii::$app->jdf::jdate('Y/m/d', $model->created) : null,
            ],
            'creator_id' => [
                'attribute' => $model->getAttributeLabel('creator_id'),
                'value' => fn(ComfortItems $model) => $model->creator?->fullName
            ],
            'update_id' => [
                'attribute' => $model->getAttributeLabel('update_id'),
                'value' => fn(ComfortItems $model) => $model->update?->fullName
            ],
            'changed' => [
                'attribute' => $model->getAttributeLabel('changed'),
                'value' => fn(ComfortItems $model) => Yii::$app->jdf::jdate('Y/m/d', $model->changed)
            ],
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
            ]
		]) ?>
	</div>
</div>
