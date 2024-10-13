<?php

use hesabro\hris\models\SalaryPeriodItems;
use common\models\BalanceDetailed;
use common\models\Settings;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $searchModel hesabro\hris\models\SalaryPeriodItemsSearch */
/* @var $searchModelUser hesabro\hris\models\EmployeeBranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderUser yii\data\ActiveDataProvider */

$style = <<<CSS
    .list-salary-items .table-responsive {
        overflow-x: unset !important;
    }

    .list-salary-items .kv-grid-container {
        min-height: auto !important;
    }
CSS;

$this->registerCss($style);
?>
<div class="card list-salary-items">
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

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => "{group}",
                    'buttons' => [
                        'group' => function ($url, SalaryPeriodItems $model, $key) {
                            $items = [];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-eye']) . ' ' . Module::t('module', "Details"),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Details'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['view', 'id' => $model->id]),
                                    'data-action' => 'view-ipg',
                                ],
                            ];
                            if ($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Update'),
                                        'data-size' => 'modal-xl',
                                        'data-title' => Module::t('module', 'Update') . ' - ' . $model->user->fullName,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['update', 'id' => $model->id]),
                                        'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    ],
                                ];
                            }
                            if ($model->canUpdateAfterConfirm()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Update'),
                                        'data-size' => 'modal-xl',
                                        'data-title' => Module::t('module', 'Update') . ' - ' . $model->user->fullName,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['update-after-confirm', 'id' => $model->id]),
                                        'data-action' => 'edit-ipg',
                                        'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    ],
                                ];
                            }
                            if ($model->canDelete()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Module::t('module', 'Delete'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                        'title' => Module::t('module', 'Delete'),
                                        'aria-label' => Module::t('module', 'Delete'),
                                        'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['delete', 'id' => $model->id]),
                                        'class' => "text-danger p-jax-btn",
                                        'data-title' => Module::t('module', 'Delete'),
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            if ($model->canPrint()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-print']) . ' ' . Module::t('module', "Print"),
                                    'url' => ['print-single-item', 'id' => $key],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'target' => '_blank',
                                        'data-pjax' => '0'
                                    ],
                                ];
                            }

                            $items[] = [
                                'label' => Html::tag('span', '', ['class' => 'fa fa-list']) . ' ' . 'مشاهده دروه های قبلی',
                                'url' => ['user', 'id' => $model->user_id],
                                'encode' => false,
                                'linkOptions' => [
                                    'data-pjax' => '0'
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Module::t('module', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => SalaryPeriodItems::class],
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Module::t('module', 'Log'),
                                    'class' => 'showModalButton',
                                    'data-size' => 'modal-xxl',
                                ],
                            ];
                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Module::t('module', 'Actions')],
                                'encodeLabel' => false,
                                'label' => '<i class="far fa-list mr-1"></i>',
                                'options' => ['class' => 'float-right'],
                                'dropdown' => [
                                    'items' => $items,
                                ],
                            ]);;
                        },
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw',
                ],
                [
                    'label' => Module::t('module', 'Shaba Number'),
                    'value' => function (SalaryPeriodItems $model) {
                        return $model->employee->shaba;
                    },
                    'format' => 'raw',
                    'visible' => $searchModel->show_iban
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
                    'label' => 'حقوق پایه',
                    'value' => function ($model) {
                        return number_format((float)($model->basic_salary * $model->hours_of_work));
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('basic_salary*hours_of_work')),
                    'format' => 'raw',
                    'visible' => $searchModel->showAccounting
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
                    'attribute' => 'cost_of_spouse',
                    'value' => function ($model) {
                        return number_format((float)$model->cost_of_spouse);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_spouse')),
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
                    'attribute' => 'rate_of_year',
                    'value' => function ($model) {
                        return number_format((float)$model->rate_of_year);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('rate_of_year')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'hours_of_overtime',
                    'value' => function (SalaryPeriodItems $model) {
                        return number_format((float)$model->cost_hours_of_overtime);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.cost_hours_of_overtime")')),
                    'format' => 'raw',
                    'visible' => $searchModel->showAccounting
                ],

                [
                    'attribute' => 'cost_of_trust',
                    'value' => function ($model) {
                        return number_format((float)$model->cost_of_trust);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_trust')),
                    'format' => 'raw',
                    'visible' => $searchModel->showAccounting
                ],

                [
                    'attribute' => 'total_salary',
                    'value' => function ($model) {
                        return number_format((float)$model->total_salary);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('total_salary')),
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
                    'attribute' => 'insurance',
                    'value' => function ($model) {
                        return number_format((float)$model->insurance);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('insurance')),
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
                    'attribute' => 'advance_money',
                    'value' => function ($model) {
                        return number_format((float)$model->advance_money);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('advance_money')),
                    'format' => 'raw',
                ],
                [
                    'label' => 'مساعده دریافتی',
                    'value' => function ($model) {
                        return number_format((float)BalanceDetailed::getBalance(Module::getInstance()->settings::get('m_debtor_advance_money'), $model->user->customer->oneAccount->id, false));
                    },
                    'format' => 'raw',

                    'visible' => $searchModel->check_advance_money,
                ],
//                [
//                    'attribute' => 'count_point',
//                    'value' => function ($model) {
//                        return $model->count_point > 0 ? $model->count_point . ' (' . number_format((float)$model->cost_point) . ') ' : null;
//                    },
//                    'format' => 'raw',
//                ],
                [
                    'attribute' => 'non_cash_commission',
                    'value' => function ($model) {
                        return number_format($model->non_cash_commission);
                    },
                    'footer' => number_format((int)$dataProvider->query->sum('non_cash_commission')),
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
            ],
        ]); ?>
    </div>
</div>
