<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\Comfort */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Comforts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comfort-view card">
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'title',
                'type',
                'expire_time:datetime',
                'status',
                'type_limit',
                'amount_limit',
                'description:ntext',
                'additional_data',
                'created',
                'creator_id',
                'update_id',
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
