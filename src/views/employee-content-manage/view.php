<?php

use common\components\Helper;
use common\components\jdf\Jdf;
use common\models\Faq;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Faq */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Faqs'), 'url' => ['index']];
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
                    'value' => Faq::itemAlias('Type', $model->type),
                ],

                ...($model->type === Faq::TYPE_ANNOUNCEMENT ? [
                    [
                        'attribute' => 'show_start_at',
                        'value' => $model->show_end_at ? Jdf::jdate('Y/m/d', $model->show_start_at) : '-',
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'show_end_at',
                        'value' => $model->show_end_at ? Jdf::jdate('Y/m/d', $model->show_end_at) : '-',
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'attachment',
                        'value' => ($attachment = $model->getFileUrl('attachment')) ?
                            Html::a(Module::t('module', 'Download'), $attachment, ['target' => '_blank', 'download' => true]) :
                            '-',
                        'format' => 'raw'
                    ],
                ] : []),

                ...(Yii::$app->client->isMaster() ? [
                    [
                        'attribute' => 'include_client_ids',
                        'format' => 'raw',
                        'value' => $model->includedClientsListView(),
                    ],
                    [
                        'attribute' => 'exclude_client_ids',
                        'format' => 'raw',
                        'value' => $model->excludedClientsListView(),
                    ],
                ] : []),
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
            <?= $model->getFileUrl('attachment') ? Html::a(Module::t('module', 'Delete attachment'), ['remove-attachment', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) : '' ?>
        </div>
    <?php endif; ?>
</div>