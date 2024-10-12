<?php

use hesabro\hris\models\ComfortItems;
use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\ComfortItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="comfort-items-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['class' => ComfortItems::itemAlias('StatusClass', $model->status)];
            },
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'comfort-p-jax']
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_detail', [
                            'model' => $model,
                        ]);
                    },
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function (ComfortItems $model) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'comfort_id',
                    'value' => function (ComfortItems $model) {
                        return $model->comfort->title;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function (ComfortItems $model) {
                        return ComfortItems::itemAlias('Status', $model->status);
                    },
                    'format' => 'raw'
                ],
                'amount:currency',
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'creator_id',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update->fullName . '">' . $model->creator->fullName . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{confirm}{reject}{undo}{cycle}{refer}{log}",
                    'buttons' => [
                        'cycle' => function ($url, ComfortItems $model, $key) {
                            return $model->comments_count ? Html::a('<i class="fas fa-recycle text-warning"></i>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Work Cycle'),
                                    'id' => 'work-cycle',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Work Cycle'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['comfort-items/comments', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'comfort-p-jax',
                                    'data-handle-form-submit' => 0,
                                    'disabled' => true
                                ]) : '';
                        },
                        'confirm' => function ($url, $model, $key) {
                            return $model->status == ComfortItems::STATUS_WAIT_CONFIRM ? ($model->canConfirm() ? Html::a('<span class="fa fa-check text-success"></span>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Confirm'),
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Confirm'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['confirm', 'id' => $model->id]),
                                    'data-reload-pjax-container' => 'comfort-p-jax',
                                    'data-handle-form-submit' => 1,
                                    'disabled' => true
                                ]) : Html::a('<span class="fa fa-check"></span>',
                                'javascript:void(0)',
                                [
                                    'data-pjax' => '0',
                                    'class' => "text-secondary alert-btn    ",
                                    //'data-alert-title' => 'post',
                                    'data-alert-text' => $model->error_msg,
                                ])) : '';
                        },
                        'refer' => function ($url, ComfortItems $model, $key) {
                            return !$model->comments_count ? Html::a('<i class="far fa-paper-plane text-black"></i>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Refer'),
                                    'id' => 'refer',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Refer'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['comfort-items/refer', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'comfort-p-jax',
                                    'data-handle-form-submit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'reject' => function ($url, ComfortItems $model, $key) {
                            return $model->canReject() ? Html::a('<span class="far fa-times text-danger"></span>',
                                "javascript:void(0)",
                                [
                                    'id' => 'reject-leave-btn',
                                    'data-size' => 'modal-lg',
                                    'title' => Module::t('module', 'Reject'),
                                    'data-title' => Module::t('module', 'Reject'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['reject', 'id' => $model->id]),
                                    'data-reload-pjax-container' => 'comfort-p-jax',
                                    'disabled' => true
                                ]) : '';
                        },
                        'undo' => function ($url, ComfortItems $model, $key) {
                            return $model->canRevert() ?
                                Html::a(Html::tag('span', '', ['class' => 'far fa-undo']), Url::to(['revert', 'id' => $model->id]),
                                    [
                                        'title' => Module::t('module', 'Undo'),
                                        'data-confirm' => Module::t('module', 'Are you sure you want to undo this item?'),
                                        'data-method' => 'post',
                                        'class' => 'ajax-btn text-danger',
                                        'data-view' => 'index',
                                        'data-p-jax' => '#comfort-p-jax'
                                    ]) : '';
                        },
                        'log' => function ($url, $model, $key) {
                            return Html::a('<span class="fas fa-history text-info"></span>',
                                ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
                                [
                                    'class' => 'text-secondary showModalButton',
                                    'title' => Module::t('module', 'Logs'),
                                    'data-size' => 'modal-xl'
                                ]
                            );
                        },

                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
