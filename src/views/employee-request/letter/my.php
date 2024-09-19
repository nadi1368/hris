<?php

use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRequestSearch;
use common\widgets\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var EmployeeRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('app', 'Request') .' '. Yii::t('app', 'Letter');
$this->params['breadcrumbs'][] = Yii::t('app', 'Profile');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'pjax-official-letter']) ?>
<div class="card">
    <div id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title mb-0">
                <a class="accordion-toggle collapsed d-flex align-items-center gap-2" data-toggle="collapse" data-parent="#accordion" href="#searchBox" aria-expanded="false">
                    <i class="far fa-search"></i>
                    <span><?= Yii::t('app', 'Search') ?></span>
                </a>
            </h4>
            <?= Html::a(Yii::t('app', 'Create'),
                'javascript:void(0)', [
                    'title' => Yii::t('app', 'Create'),
                    'class' => 'btn btn-success',
                    'data-size' => 'modal-md',
                    'data-title' => Yii::t('app', 'Request') .' '. Yii::t('app', 'Letter'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['create', 'type' => EmployeeRequest::TYPE_LETTER]),
                    'data-reload-pjax-container-on-show' => 0,
                    'data-reload-pjax-container' => 'pjax-official-letter',
                    'data-handleFormSubmit' => 1,
                    'disabled' => true
                ]); ?>
        </div>
        <div id="searchBox" class="panel-collapse collapse" aria-expanded="false"></div>
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
                        return Yii::$app->controller->renderPartial('letter/_detail-my', [
                            'model' => $model,
                        ]);
                    },
                ],
                [
                    'class' => 'yii\grid\SerialColumn'
                ],
                [
                    'attribute' => 'contract_template_id',
                    'value' => fn (EmployeeRequest $model) => $model->contractTemplate?->title
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
                    'template' => '{update}{delete}{view}{print}',
                    'buttons' => [
                        'update' => function ($url, EmployeeRequest $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="far fa-edit text-success"></span>',
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Update'),
                                    'class' => 'text-success',
                                    'data-size' => 'modal-md',
                                    'data-title' => Yii::t('app', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => 'pjax-official-letter',
                                    'data-handle-form-submit' => 1
                                ]) : '';
                        },
                        'delete' => function ($url, EmployeeRequest $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), Url::to(['delete', 'id' => $model->id]),
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'class' => 'ajax-btn text-danger',
                                    'data-view' => 'index',
                                    'data-p-jax' => '#pjax-official-letter'
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
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>
