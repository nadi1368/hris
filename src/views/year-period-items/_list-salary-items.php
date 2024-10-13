<?php

use hesabro\hris\Module;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $searchModel hesabro\hris\models\SalaryPeriodItemsSearch */
/* @var $searchModelUser hesabro\hris\models\EmployeeBranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderUser yii\data\ActiveDataProvider */

?>
<div class="card">
    <div class="panel-group m-bot20" id="accordionTwo">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordionTwo"
                   href="#collapseTwo" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'showFooter' => true,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw',
                ],
                'hours_of_work',
                [
                    'attribute' => 'basic_salary',
                    'value' => function ($model) {
                        return number_format((float)$model->basic_salary);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('basic_salary')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cost_of_house',
                    'value' => function ($model) {
                        return number_format((float)$model->cost_of_house);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_house')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cost_of_food',
                    'value' => function ($model) {
                        return number_format((float)$model->cost_of_food);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_food')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cost_of_children',
                    'value' => function ($model) {
                        return number_format((float)$model->cost_of_children);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_children')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'insurance',
                    'value' => function ($model) {
                        return number_format((float)$model->insurance);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('insurance')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'insurance_owner',
                    'value' => function ($model) {
                        return number_format((float)$model->insurance_owner);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('insurance_owner')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'tax',
                    'value' => function ($model) {
                        return number_format((float)$model->tax);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('tax')),
                    'format' => 'raw',
                ],
//                        [
//                            'attribute' => 'cost_of_trust',
//                            'value' => function ($model) {
//                                return number_format((float)$model->cost_of_trust);
//                            },
//                            'footer' => number_format((float)$dataProvider->query->sum('cost_of_trust')),
//                            'format' => 'raw',
//                        ],
                [
                    'attribute' => 'total_salary',
                    'value' => function ($model) {
                        return number_format((float)$model->total_salary);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('total_salary')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'advance_money',
                    'value' => function ($model) {
                        return number_format((float)$model->advance_money);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('advance_money')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'count_point',
                    'value' => function ($model) {
                        return $model->count_point>0 ? $model->count_point.' ('.number_format((float)$model->cost_point).') ' : null;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'payment_salary',
                    'value' => function ($model) {
                        return number_format((float)$model->payment_salary);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('payment_salary')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'final_payment',
                    'value' => function (SalaryPeriodItems $model) {
                        return number_format((int)$model->finalPayment);
                    },
                    'footer' => number_format((int)$dataProvider->query->sum(SalaryPeriodItems::getFinalPaymentStringAttributes())),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'can_payment',
                    'value' => function ($model) {
                        return Html::a(Html::tag('span', '', ['class' => $model->can_payment ? "fa fa-check" : "fa fa-times"]), 'javascript:void(0)',
                            [
                                'title' => !$model->can_payment ? Module::t('module', 'Add To Payment List') : Module::t('module', 'Delete Payment List'),
                                'aria-label' => !$model->can_payment ? Module::t('module', 'Add To Payment List') : Module::t('module', 'Delete Payment List'),
                                'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                'data-pjax' => '0',
                                'data-url' => Url::to(['add-to-payment-list', 'id' => $model->id, 'type' => $model->can_payment ? Yii::$app->helper::UN_CHECKED : Yii::$app->helper::CHECKED]),
                                'class' => $model->can_payment ? "text-success p-jax-btn" : "text-danger p-jax-btn",
                                'data-title' => !$model->can_payment ? Module::t('module', 'Add To Payment List') : Module::t('module', 'Delete Payment List'),
                                'data-method' => 'post',

                            ]);

                    },
                    'format' => 'raw',
                ],
                //'cost_of_house',
                //'cost_of_food',
                //'cost_of_children',
                //'count_of_children',
                //'cost_of_year',
                //'rate_of_year',
                //'hours_of_overtime:datetime',
                //'rate_of_overtime',
                //'commission',
                //'creator_id',
                //'update_id',
                //'created',
                //'changed',
                //'total_salary',
                //'advance_money',
                //'payment_salary',

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{view} {update}{delete}{log}{updateAfterConfirm}",
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="fa fa-eye text-info"></span>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Details'),
                                    'id' => 'view-ipg-btn',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Details'),
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
                                    'title' => Module::t('module', 'Update'),
                                    'id' => 'edit-ipg-btn',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Update').' - '.$model->user->fullName,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'updateAfterConfirm' => function ($url, $model, $key) {
                            return $model->canUpdateAfterConfirm() ? Html::a('<span class="fa fa-edit text-primary"></span>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Update'),
                                    'id' => 'edit-ipg-btn',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Update').' - '.$model->user->fullName,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update-after-confirm', 'id' => $model->id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Module::t('module', 'Delete'),
                                    'aria-label' => Module::t('module', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Module::t('module', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        },

                        'log' => function ($url, $model, $key) {
                            return Html::a('<span class="fas fa-history text-info"></span>',
                                ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => SalaryPeriodItems::class],
                                [
                                    'class' => 'text-secondary showModalButton',
                                    'title' => Module::t('module', 'Logs'),
                                    'data-size' => 'modal-xl'
                                ]
                            );
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
