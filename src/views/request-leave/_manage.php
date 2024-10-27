<?php

use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\models\RequestLeave;
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var RequestLeaveSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

Pjax::begin(['id' => 'p-jax-request-leave']);
?>
<div class="request-leave-index card">
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
            'rowOptions'   => function ($model, $index, $widget, $grid) {
                return ['class' => RequestLeave::itemAlias('StatusClass', $model->status)];
            },
            //'filterModel' => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
//                'id',
//                'branch_id',
//                'user_id',
//                'manager_id',
                [
                    'attribute' => 'user_id',
                    'value'     => function ($model, $key, $index, $widget) {
                        return Html::a($model->user->getLink()) .
                            Html::a('<span class="far fa-info-square text-' . RequestLeave::itemAlias('StatusClass', $model->status) . ' ml-2"></span>',
                                "javascript:void(0)",
                                [
                                    'id'                         => 'create-leave-daily-btn',
                                    'class'                      => 'fas fa-report',
                                    'data-size'                  => 'modal-lg',
                                    'title'                      => Module::t('module', 'leave report'),
                                    'data-title'                 => Module::t('module', 'leave report'),
                                    'data-toggle'                => 'modal',
                                    'data-target'                => '#modal-pjax',
                                    'data-url'                   => Url::to(['sum-merit', 'id' => $key]),
                                    'data-reload-pjax-container' => 'p-jax-request-leave',
                                    'disabled'                   => true
                                ]);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'type',
                    'value'     => function ($model) {
                        return RequestLeave::itemAlias('Types', $model->type);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value'     => function ($model) {
                        return RequestLeave::itemAlias('Status', $model->status);
                    },
                    'format'    => 'raw'
                ],
                'description:ntext',
                [
                    'attribute' => 'from_date',
                    'value'     => function ($model) {
                        return in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("Y/m/d", $model->from_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->from_date);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'to_date',
                    'value'     => function ($model) {
                        return in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("Y/m/d", $model->to_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->to_date);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'range',
                    'value'     => function ($model) {
                        return Yii::$app->formatter->asDuration($model->to_date - $model->from_date, '  و ');
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'created',
                    'value'     => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'creator_id',
                    'value'     => function ($model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update->fullName . '">' . $model->creator->fullName . '</span>';
                    },
                    'format'    => 'raw'
                ],

                [
                    'class'    => 'common\widgets\grid\ActionColumn',
                    'template' => '{confirm}{reject}{view}{delete}{update}{log}',
                    'buttons'  => [
                        'view'    => function ($url, $model, $key) {
                            return Html::a('<span class="far fa-eye text-info"></span>', ['view', 'id' => $key], [
                                'title' => Yii::t('yii', 'View'),
                                'class' => 'target'
                            ]);
                        },
                        'reject'  => function ($url, $model, $key) {
                            return $model->canChangeStatus(RequestLeave::STATUS_REJECT_MANAGER_BRANCH) ? Html::a('<span class="far fa-times text-danger"></span>',
                                "javascript:void(0)",
                                [
                                    'id'                         => 'reject-leave-btn',
                                    'data-size'                  => 'modal-lg',
                                    'title'                      => Module::t('module', 'Reject'),
                                    'data-title'                 => Module::t('module', 'Reject'),
                                    'data-toggle'                => 'modal',
                                    'data-target'                => '#modal-pjax',
                                    'data-url'                   => Url::to(['reject', 'id' => $key, 'status' => RequestLeave::STATUS_REJECT_MANAGER_BRANCH]),
                                    'data-reload-pjax-container' => 'p-jax-request-leave',
                                    'disabled'                   => true
                                ]) : '';
                        },
                        'confirm' => function ($url, $model, $key) {
                            return $model->canChangeStatus(RequestLeave::STATUS_CONFIRM_MANAGER_BRANCH) ? Html::a('<span class="fa fa-check text-success"></span>', ['confirm', 'id' => $key, 'status' => RequestLeave::STATUS_CONFIRM_MANAGER_BRANCH], [
                                'title'        => Module::t('module', 'Confirm'),
                                'data-confirm' => Module::t('module', 'Are you sure?'),
                                'data-method'  => 'post',
                                'class'        => 'ajax-btn',
                                'data-view'    => 'index',
                                'data-p-jax'   => '#p-jax-request-leave',
                            ]) : '';
                        },
                        'update' => function ($url,RequestLeave  $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="far fa-edit text-success"></span>',
                                "javascript:void(0)",
                                [
                                    'id'                         => 'update-leave-daily-btn',
                                    'data-size'                  => 'modal-lg',
                                    'data-title'                 => Yii::t('yii', 'Update'),
                                    'data-toggle'                => 'modal',
                                    'data-target'                => '#modal-pjax',
                                    'data-url'                   => !$model->isNewRecord && in_array($model->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ?
                                        Url::to(['update-daily', 'id' => $key]) : Url::to(['update', 'id' => $key]),
                                    'data-reload-pjax-container' => 'p-jax-request-leave',
                                    'disabled'                   => true
                                ]) : '';
                        },
                        'delete' => function ($url,RequestLeave  $model, $key) {
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
<?php Pjax::end() ?>
