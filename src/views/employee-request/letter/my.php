<?php

use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRequestSearch;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var EmployeeRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Module::t('module', 'Request') .' '. Module::t('module', 'Letter');
$this->params['breadcrumbs'][] = Module::t('module', 'Profile');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'pjax-official-letter']) ?>
<div class="card">
    <div id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title mb-0">
                <a class="accordion-toggle collapsed d-flex align-items-center gap-2" data-toggle="collapse" data-parent="#accordion" href="#searchBox" aria-expanded="false">
                    <i class="far fa-search"></i>
                    <span><?= Module::t('module', 'Search') ?></span>
                </a>
            </h4>
            <?= Html::a(Module::t('module', 'Create'),
                'javascript:void(0)', [
                    'title' => Module::t('module', 'Create'),
                    'class' => 'btn btn-success',
                    'data-size' => 'modal-md',
                    'data-title' => Module::t('module', 'Request') .' '. Module::t('module', 'Letter'),
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
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->updated_at) . '">' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created_at) . '</span>';
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
                                    'title' => Module::t('module', 'Update'),
                                    'class' => 'text-success',
                                    'data-size' => 'modal-md',
                                    'data-title' => Module::t('module', 'Update'),
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
                                    'title' => Module::t('module', 'View'),
                                    'class' => 'text-primary',
                                    'data-size' => 'modal-lg',
                                    'data-title' => implode(' ', [
                                        Module::t('module', 'View'),
                                        Module::t('module', 'Letter'),
                                        "({$model->contractTemplate?->title})",
                                        Module::t('module', 'For'),
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
                                    'title' => Module::t('module', 'Print'),
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
