<?php

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $dataProviderUser yii\data\ActiveDataProvider */
/* @var $searchModelUser hesabro\hris\models\EmployeeBranchSearch */
?>
<div class="employee-branch-index card">
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
            <?= $this->render('_search_user', ['model' => $searchModelUser]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProviderUser,
            //'filterModel' => $searchModel,
            'columns' => [
//                            ['class' => 'yii\grid\SerialColumn'],

//                        'job_code',
//                        [
//                            'attribute' => 'branch_id',
//                            'value' => function ($model) {
//                                return $model->branch->title;
//                            },
//                            'format' => 'raw',
//                        ],
                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{create}{insurance_data}",
                    'contentOptions' => [ 'width' => '20px'],
                    'buttons' => [
                        'create' => function ($url, EmployeeBranchUser $model, $key) use ($salaryPeriod) {
                            return $salaryPeriod->canCreateItems() && $model->canCreateSalaryPayment() ? Html::a(Html::tag('span', ' ', ['class' => 'fa fa-plus']) ,
                                'javascript:void(0)', [
                                    'title' => $model->user->fullName,
                                    'class' => "btn btn-success btn-md",
                                    'style' => 'padding: 3px 7px !important;',
                                    'data-size' => 'modal-xl',
                                    'data-title' => $model->user->fullName,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['create', 'period_id' => $salaryPeriod->id, 'user_id' => $model->user_id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : Html::a(Html::tag('span', ' ', ['class' => 'fa fa-plus']),
                                'javascript:void(0)',
                                [
                                    'data-pjax' => '0',
                                    'class' => "btn btn-secondary btn-md alert-btn",
                                    'style' => 'padding: 3px 7px !important;',
                                    //'data-alert-title' => 'post',
                                    'data-alert-text' => $model->error_msg ?:  Module::t('module', 'It is not possible to perform this operation'),
                                ]);
                        },
                        'insurance_data' => function ($url, EmployeeBranchUser $model, $key) {
                            if ($model->canUpdate()) {
                                $items = [];
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
                                        'data-url' => Url::to([Module::createUrl('default/update-user'), 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                                        'data-reload-pjax-container' => "p-jax-salary-period-items",
                                    ],
                                ];
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Insurance Data'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Insurance Data'),
                                        'data-size' => 'modal-xl',
                                        'data-title' => Module::t('module', 'Insurance Data') . ' - ' . $model->user->fullName,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to([Module::createUrl('default/insurance-data'), 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                                        'data-reload-pjax-container' => "p-jax-salary-period-items",
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
                                ]);
                            }
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
