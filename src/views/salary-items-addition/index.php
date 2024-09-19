<?php

use hesabro\hris\models\SalaryItemsAddition;
use common\components\Helper;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\SalaryItemsAdditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Salary Items Additions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-items-addition-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a('آپلود فایل مزایای غیر نقدی', ['salary-items-addition/upload-salary-non-cash'], ['class' => 'btn btn-primary']); ?>
                <?php foreach (SalaryItemsAddition::itemAlias('Kind') as $kind => $title): ?>
                    <?= Html::a(Yii::t('app', 'Create') . ' ' . $title,
                        'javascript:void(0)', [
                            'title' => Yii::t('app', 'Create'),
                            'id' => 'create-addition-' . $kind,
                            'class' => 'btn btn-primary',
                            'data-size' => 'modal-lg',
                            'data-title' => Yii::t('app', 'Create') . ' ' . $title,
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax',
                            'data-url' => Url::to(['create', 'kind' => $kind]),
                            'data-reload-pjax-container-on-show' => 0,
                            'data-reload-pjax-container' => 'salary-items-addition',
                            'data-handleFormSubmit' => 1,
                            'disabled' => true
                        ]); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?php Pjax::begin(['id' => 'salary-items-addition']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'toolbar' => [],
            'showCustomToolbar' => true,
            'showCreateBtnAtToolbar' => false,
            'showDeleteBtnAtToolbar' => false,
            'showConfirmBtnAtToolbar' => true,
            'reloadPjaxContainer' => 'salary-items-addition',
            'layout' => "{toolbar}\n{summary}\n<div class='table-responsive mb-2'>{items}</div>{pager}",
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['class' => SalaryItemsAddition::itemAlias('StatusClass', $model->status)];
            },
            'columns' => [
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_index', [
                            'model' => $model,
                        ]);
                    },
                ],
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'user_id',
                    'value' => function (SalaryItemsAddition $model, $key, $index, $widget) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'kind',
                    'value' => function (SalaryItemsAddition $model) {
                        return SalaryItemsAddition::itemAlias('Kind', $model->kind);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'type',
                    'value' => function (SalaryItemsAddition $model) {
                        return SalaryItemsAddition::itemAlias('Type', $model->type);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'second',
                    'label' => 'مقدار',
                    'value' => function (SalaryItemsAddition $model) {
                        return $model->getValue();
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'date',
                    'value' => function (SalaryItemsAddition $model) {
                        return $model->getDate();
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function (SalaryItemsAddition $model) {
                        return SalaryItemsAddition::itemAlias('Status', $model->status);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'is_auto',
                    'value' => function (SalaryItemsAddition $model) {
                        return Helper::itemAlias('CheckboxIcon', $model->is_auto);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{update}{delete}{confirm}{reject}{log}{returnStatus}",
                    'buttons' => [
                        'update' => function ($url, SalaryItemsAddition $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="fa fa-edit text-primary"></span>',
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Update'),
                                    'data-size' => 'modal-lg',
                                    'data-title' => Yii::t('app', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'salary-items-addition',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url, SalaryItemsAddition $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'salary-items-addition',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Yii::t('yii', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        },
                        'log' => function ($url, SalaryItemsAddition $model, $key) {
                            return Html::a('<span class="fas fa-history text-info"></span>',
                                ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
                                [
                                    'class' => 'text-secondary showModalButton',
                                    'title' => Yii::t('app', 'Logs'),
                                    'data-size' => 'modal-xl'
                                ]
                            );
                        },
                        'reject' => function ($url, SalaryItemsAddition $model, $key) {
                            return $model->canReject() ? Html::a('<span class="far fa-times text-danger"></span>',
                                "javascript:void(0)",
                                [
                                    'id' => 'reject-leave-btn',
                                    'data-size' => 'modal-lg',
                                    'title' => Yii::t('app', 'Reject'),
                                    'data-title' => Yii::t('app', 'Reject'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['reject', 'id' => $model->id]),
                                    'data-reload-pjax-container' => 'salary-items-addition',
                                    'disabled' => true
                                ]) : '';
                        },
                        'confirm' => function ($url, SalaryItemsAddition $model, $key) {
                            return $model->canConfirm() ? Html::a('<span class="fa fa-check text-success"></span>', ['confirm', 'id' => $model->id], [
                                'title' => Yii::t('app', 'Confirm'),
                                'data-confirm' => Yii::t('app', 'Are you sure?'),
                                'data-method' => 'post',
                                'class' => 'ajax-btn',
                                'data-view' => 'index',
                                'data-p-jax' => '#salary-items-addition',
                            ]) : '';
                        },
                        'returnStatus' => function ($url, SalaryItemsAddition $model, $key) {
                            return $model->canReturnStatus() ? Html::a('<span class="fa fa-undo text-warning"></span>', ['return-status', 'id' => $model->id], [
                                'title' => Yii::t('app', 'Return State'),
                                'data-confirm' => Yii::t('app', 'Are you sure?'),
                                'data-method' => 'post',
                                'class' => 'ajax-btn',
                                'data-view' => 'index',
                                'data-p-jax' => '#salary-items-addition',
                            ]) : '';
                        }
                    ]
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
