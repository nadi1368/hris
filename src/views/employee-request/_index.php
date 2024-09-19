<?php

use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRequestSearch;
use common\widgets\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;

/**
 * @var EmployeeRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var View $this
 */
Pjax::begin(['id' => 'pjax-employee-request']);
?>
<div class="card">
    <div id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title mb-0">
                <a class="accordion-toggle collapsed d-flex align-items-center gap-2" data-toggle="collapse" data-parent="#accordion" href="#searchBox" aria-expanded="false">
                    <i class="far fa-search"></i>
                    <span><?= Yii::t('app', 'Search') ?></span>
                </a>
            </h4>
        </div>
        <div id="searchBox" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model) {
                return [
                    'class' => EmployeeRequest::itemAlias('StatusClass', $model->status)
                ];
            },
            'columns' => [
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => fn ($model, $key, $index, $column) => GridView::ROW_COLLAPSED,
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_detail', [
                            'model' => $model,
                        ]);
                    },
                ],
                [
                    'class' => 'yii\grid\SerialColumn'
                ],
                [
                    'attribute' => 'user_id',
                    'value' => fn (EmployeeRequest $model) => $model->user->getLink(),
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'type',
                    'value' => fn(EmployeeRequest $model) => EmployeeRequest::itemAlias('Type', $model->type),
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => fn (EmployeeRequest $model) => EmployeeRequest::itemAlias('Status', $model->status),
                    'filter' => EmployeeRequest::itemAlias('Status')
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function (EmployeeRequest $model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->updated_at) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created_at) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{confirm}{reject}{view}{print}{undo}',
                    'buttons' => [
                        'confirm' => function ($url, EmployeeRequest $model, $key) {
                            return $model->canConfirm() ? Html::a(Html::tag('span', '', ['class' => 'far fa-check']),
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Confirm'),
                                    'class' => 'text-success',
                                    'data-size' => 'modal-xl',
                                    'data-title' => implode(' ', [
                                        Yii::t('app', 'Confirm'),
                                        Yii::t('app', 'Letter'),
                                        $model->contractTemplate?->title
                                    ]),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['confirm', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => 'pjax-employee-request',
                                    'data-handle-form-submit' => 1
                                ]) : '';
                        },
                        'reject' => function ($url, EmployeeRequest $model, $key) {
                            return $model->canConfirm() ? Html::a(Html::tag('span', '', ['class' => 'fa fa-times']),
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Reject'),
                                    'class' => 'text-danger',
                                    'data-size' => 'modal-md',
                                    'data-title' => implode(' ', [
                                        Yii::t('app', 'Reject'),
                                        Yii::t('app', 'Request'),
                                        Yii::t('app', 'Letter'),
                                        $model->contractTemplate?->title
                                    ]),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['reject', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => 'pjax-employee-request',
                                    'data-handle-form-submit' => 1
                                ]) : '';
                        },
                        'undo' => function ($url, EmployeeRequest $model, $key) {
                            return in_array($model->status, [EmployeeRequest::STATUS_ACCEPT, EmployeeRequest::STATUS_REJECT]) ?
                                Html::a(Html::tag('span', '', ['class' => 'far fa-undo']), Url::to(['undo', 'id' => $model->id]),
                                    [
                                        'title' => Yii::t('app', 'Undo'),
                                        'data-confirm' => Yii::t('app', 'Are you sure you want to undo this item?'),
                                        'data-method' => 'post',
                                        'class' => 'ajax-btn text-danger',
                                        'data-view' => 'index',
                                        'data-p-jax' => '#pjax-employee-request'
                                    ]) : '';
                        },
                        'view' => function ($url, EmployeeRequest $model, $key) {
                            return $model->status == EmployeeRequest::STATUS_ACCEPT ? Html::a(Html::tag('span', '', ['class' => 'far fa-eye']),
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'View'),
                                    'class' => 'text-primary',
                                    'data-size' => 'modal-lg',
                                    'data-title' => implode(' ', [
                                        Yii::t('app', 'View'),
                                        Yii::t('app', 'Letter'),
                                        "({$model->contractTemplate?->title})",
                                        Yii::t('app', 'For'),
                                        $model->user->fullName
                                    ]),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['view', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'pjax-employee-request',
                                    'data-handle-form-submit' => 0
                                ]) : '';
                        },
                        'print' => function ($url, EmployeeRequest $model, $key) {
                            return $model->status === EmployeeRequest::STATUS_ACCEPT ? Html::a(Html::tag('span', '', ['class' => 'fas fa-print']), Url::to(['view', 'id' => $model->id, 'print' => 1]),
                                [
                                    'title' => Yii::t('app', 'Print'),
                                    'class' => 'text-info popup-link',
                                    'data' => [
                                        'popup-width' => '620',
                                        'popup-height' => '877'
                                    ]
                                ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>

