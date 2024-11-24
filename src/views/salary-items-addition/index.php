<?php

use hesabro\hris\models\SalaryItemsAddition;
use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap4\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\SalaryItemsAdditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Salary Items Additions');
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
                    <?= Html::a(Module::t('module', 'Create') . ' ' . $title,
                        'javascript:void(0)', [
                            'title' => Module::t('module', 'Create'),
                            'id' => 'create-addition-' . $kind,
                            'class' => 'btn btn-primary',
                            'data-size' => 'modal-lg',
                            'data-title' => Module::t('module', 'Create') . ' ' . $title,
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
                        return Yii::$app->helper::itemAlias('CheckboxIcon', $model->is_auto);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, SalaryItemsAddition $model, $key) {
                            $items = [];

                            if($model->canUpdate()) {
                                $items[] = [
                                        'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Yii::t('app', 'Update'),
                                        'url' => 'javascript:void(0)',
                                        'encode' => false,
                                        'linkOptions' => [
                                            'title' => Yii::t('app', 'Update'),
                                            'data-title' => Yii::t('app', 'Update'),
                                            'data-size' => 'modal-lg',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal-pjax',
                                            'data-url' => Url::to(['update', 'id' => $key]),
                                            'data-reload-pjax-container-on-show' => 0,
                                            'data-reload-pjax-container' => 'salary-items-addition',
                                        ],
                                    ];
                                }
                            if($model->canDelete())
                            {
                                $items[] = [
                                    'label' =>  Html::tag('span', '', ['class' => 'fa fa-trash-alt']) .' '. Yii::t('app', 'Delete'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => ['title' => Module::t('module', 'Delete'),
                                        'aria-label' => Module::t('module', 'Delete'),
                                        'data-reload-pjax-container' => 'salary-items-addition',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['delete', 'id' => $model->id]),
                                        'class' => " text-danger p-jax-btn",
                                        'data-title' => Module::t('module', 'Delete'),
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            if($model->canConfirm())
                            {
                                $items[] = [
                                    'label' =>  Html::tag('span', '', ['class' => 'fa fa-check-circle']) .' '.Module::t('module', 'Confirm'),
                                    'url' => ['confirm', 'id' => $model->id],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Confirm'),
                                        'data-confirm' => Module::t('module', 'Are you sure?'),
                                        'data-method' => 'post',
                                        'class' => 'ajax-btn',
                                        'data-view' => 'index',
                                        'data-p-jax' => '#salary-items-addition',
                                    ],
                                ];
                            }
                            if($model->canReject())
                            {
                                $items[] = [
                                    'label' =>  Html::tag('span', '', ['class' => 'fa fa-minus-circle']) .' '. Module::t('module', 'Reject'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-size' => 'modal-lg',
                                        'title' => Module::t('module', 'Reject'),
                                        'data-title' => Module::t('module', 'Reject'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['reject', 'id' => $model->id]),
                                        'data-reload-pjax-container' => 'salary-items-addition',
                                        'disabled' => true
                                    ],
                                ];
                            }
                            if($model->canReturnStatus())
                            {
                                $items[] = [
                                    'label' =>  Html::tag('span', '', ['class' => 'fa fa-undo']) .' '. Module::t('module', 'Return State'),
                                    'url' => ['return-status', 'id' => $model->id],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Return State'),
                                        'data-confirm' => Module::t('module', 'Are you sure?'),
                                        'data-method' => 'post',
                                        'class' => 'ajax-btn',
                                        'data-view' => 'index',
                                        'data-p-jax' => '#salary-items-addition',
                                    ],
                                ];
                            }
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) .' '. Yii::t('app', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => SalaryItemsAddition::OLD_CLASS_NAME],
                                'encode' => false,
                                'linkOptions' => [
                                    'class' => 'showModalButton',
                                    'title' => Module::t('module', 'Logs'),
                                    'data-size' => 'modal-xl'
                                ],
                            ];

                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Yii::t('app', 'Actions')],
                                'encodeLabel' => false,
                                'label' => '<i class="far fa-list mr-1"></i>',
                                'options' => ['class' => 'float-right'],
                                'dropdown' => [
                                    'items' => $items,
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
