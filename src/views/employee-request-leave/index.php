<?php

use hesabro\hris\models\RequestLeave;
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel RequestLeaveSearch */

$this->title = Module::t('module', 'Request Leaves');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'p-jax-request-leave']) ?>
<div class="request-leave-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <div class="d-flex justify-content-start">
                <div class="list-group-item d-flex justify-content-between">
                    <h6 class="mr-2 my-0"><?= Module::t('module', 'Sum of Your Merit leave in this month') ?></h6>
                    <span class=" badge badge-success text-light my-0"><?= Yii::$app->formatter->asDuration($searchModel->sumMeritLeaves()['current_month'], '  و '); ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between">
                    <h6 class="mr-2 my-0"><?= Module::t('module', 'Sum of Your Merit leave in this Year') ?></h6>
                    <span class=" badge badge-info text-light my-0"><?= Yii::$app->formatter->asDuration($searchModel->sumMeritLeaves()['current_year'], '  و '); ?></span>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <div class="mr-2">
                    <?= Html::a(Module::t('module', 'Request Hourly Leave Create'),
                        "javascript:void(0)",
                        [
                            'id' => 'create-leave-daily-btn',
                            'class' => 'btn btn-success',
                            'data-size' => 'modal-lg',
                            'data-title' => Module::t('module', 'Request Hourly Leave Create'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax',
                            'data-url' => Url::to(['create']),
                            'data-reload-pjax-container' => 'p-jax-request-leave',
                            'disabled' => true
                        ]) ?>
                </div>

                <div>
                    <?= Html::a(Module::t('module', 'Request Daily Leave Create'),
                        "javascript:void(0)",
                        [
                            'id' => 'create-leave-daily-btn',
                            'class' => 'btn btn-info',
                            'data-size' => 'modal-lg',
                            'data-title' => Module::t('module', 'Request Daily Leave Create'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax',
                            'data-url' => Url::to(['create-daily']),
                            'data-reload-pjax-container' => 'p-jax-request-leave',
                            'disabled' => true
                        ]) ?>
                </div>
            </div>
        </div>
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_user_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['class' => RequestLeave::itemAlias('StatusClass', $model->status)];
            },
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

//                'id',
//                'branch_id',
//                'user_id',
//                'manager_id',
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        return RequestLeave::itemAlias('Types', $model->type);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return RequestLeave::itemAlias('Status', $model->status);
                    },
                    'format' => 'raw'
                ],
//                'description:ntext',
                [
                    'attribute' => 'from_date',
                    'value' => function ($model) {
                        return in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("Y/m/d", $model->from_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->from_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'to_date',
                    'value' => function ($model) {
                        return in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("Y/m/d", $model->to_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->to_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'range',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDuration($model->to_date - $model->from_date, '  و ');
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
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
                    'template' => '{delete}{update}{view}',
                    'buttons' => [
                        'view' => function ($url, RequestLeave $model, $key) {
                            return Html::a('<span class="far fa-eye text-info"></span>', ['view', 'id' => $key], [
                                'title' => Yii::t('yii', 'View'),
                                'class' => 'target'
                            ]);
                        },
                        'update' => function ($url, RequestLeave $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="far fa-edit text-success"></span>',
                                "javascript:void(0)",
                                [
                                    'id' => 'update-leave-daily-btn',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Yii::t('yii', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => !$model->isNewRecord && in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ?
                                        Url::to(['update-daily', 'id' => $key]) : Url::to(['update', 'id' => $key]),
                                    'data-reload-pjax-container' => 'p-jax-request-leave',
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url, RequestLeave $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-request-leave',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => "text-danger p-jax-btn",
                                    'data-title' => Yii::t('yii', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end() ?>
