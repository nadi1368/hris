<?php

use hesabro\hris\models\EmployeeContent;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model EmployeeContent */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Contents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-view card">
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'title:ntext',
                [
                    'attribute' => 'description',
                    'format' => 'html',
                    'value' => $model->getContent(),
                ],
                [
                    'attribute' => 'type',
                    'value' => EmployeeContent::itemAlias('Type', $model->type),
                ],
                ...($model->type === EmployeeContent::TYPE_ANNOUNCEMENT ? [
                    [
                        'attribute' => 'show_start_at',
                        'value' => $model->show_end_at ? Yii::$app->jdf::jdate('Y/m/d', $model->show_start_at) : '-',
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'show_end_at',
                        'value' => $model->show_end_at ? Yii::$app->jdf::jdate('Y/m/d', $model->show_end_at) : '-',
                        'format' => 'html'
                    ],
                ] : []),
                [
                    'attribute' => 'attachment',
                    'value' => ($attachment = $model->getFileUrl('attachment')) ?
                        Html::a(Module::t('module', 'Download'), $attachment, ['target' => '_blank', 'download' => true]) :
                        '-',
                    'format' => 'raw'
                ],
            ],
        ]) ?>
    </div>
    <?php if ($model->canDelete()) : ?>
        <div class="card-footer">
            <?php // Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
            ?>
            <?= Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= $model->getFileUrl('attachment') ? Html::a(Module::t('module', 'Delete Attachment'), ['remove-attachment', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) : '' ?>
        </div>
    <?php endif; ?>
</div>