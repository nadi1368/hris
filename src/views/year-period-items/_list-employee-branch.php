<?php

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $searchModel hesabro\hris\models\SalaryPeriodItemsSearch */
/* @var $searchModelUser hesabro\hris\models\EmployeeBranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderUser yii\data\ActiveDataProvider */

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
                    'contentOptions' => ['class' => 'check_stock', 'width' => '30px'],
                    'buttons' => [
                        'create' => function ($url, EmployeeBranchUser $model, $key) use ($salaryPeriod) {
                            return $salaryPeriod->canCreateItems() && $model->canCreateYearPayment() ? Html::a('<span class="fa fa-plus"></span>',
                                'javascript:void(0)', [
                                    'title' => $model->user->fullName,
                                    'id' => 'edit-ipg-btn',
                                    'class' => $model->end_work ? 'text-warning' : 'text-success',
                                    'data-size' => 'modal-xl',
                                    'data-title' => $model->user->fullName,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['create', 'period_id' => $salaryPeriod->id, 'user_id' => $model->user_id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'insurance_data' => function ($url, $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="far fa-edit"></span>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Insurance Data'),
                                    'id' => 'insurance-data' . $model->user_id,
                                    'class' => 'text-primary',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Insurance Data'). ' - '.$model->user->fullName,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to([Module::createUrl('default/insurance-data'), 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => "p-jax-salary-period-items",
                                    'data-handle-form-submit' => 1,
                                    'disabled' => true,
                                ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>