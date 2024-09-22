<?php

use hesabro\hris\models\Comfort;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\ComfortSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Comforts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comfort-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(Yii::t('app', 'Create'),
                    'javascript:void(0)', [
                        'title' => Yii::t('app', 'Create'),
                        'id' => 'create-payment-period',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-xl',
                        'data-title' => Yii::t('app', 'Create'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container-on-show' => 0,
                        'data-reload-pjax-container' => 'p-jax-comfort',
                        'data-handleFormSubmit' => 1,
                        'disabled' => true
                    ]); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'p-jax-comfort']
            ],
            //'filterModel' => $searchModel,
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
                        return Yii::$app->controller->renderPartial('_index', [
                            'model' => $model,
                        ]);
                    },
                ],
                'id',
                'title',
                [
                    'attribute' => 'type',
                    'value' => function (Comfort $model) {
                        return Comfort::itemAlias('TypeCat',$model->type);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'type_limit',
                    'value' => function (Comfort $model) {
                        return Comfort::itemAlias('TypeLimit',$model->type_limit);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'expire_time',
                    'value' => function (Comfort $model) {
                        return $model->expire_time ? Yii::$app->jdf->jdate("Y/m/d", $model->expire_time) : null;
                    },
                    'format' => 'raw'
                ],
                //'description:ntext',
                //'additional_data',
                //'created',
                //'creator_id',
                //'update_id',
                //'changed',
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{update}{delete}{log}",
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="fa fa-eye text-info"></span>',
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Details'),
                                    'id' => 'view-ipg-btn',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Yii::t('app', 'Details'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['view', 'id' => $model->id]),
                                    'data-action' => 'view-ipg',
                                    'data-handleFormSubmit' => 0,
                                    'disabled' => true
                                ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="fa fa-edit text-primary"></span>',
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Update'),
                                    'id' => 'edit-ipg-btn',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Yii::t('app', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'p-jax-comfort',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-comfort',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Yii::t('yii', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        },
                        'log' => function ($url, $model, $key) {
                            return Html::a('<span class="fas fa-history text-info"></span>',
                                ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
                                [
                                    'class' => 'text-secondary showModalButton',
                                    'title' => Yii::t('app', 'Logs'),
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
