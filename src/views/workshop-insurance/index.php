<?php

use hesabro\hris\models\WorkshopInsurance;
use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\WorkshopInsuranceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Workshop Insurances');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workshop-insurance-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(Module::t('module', 'Create'),
                    'javascript:void(0)', [
                        'title' => Module::t('module', 'Create'),
                        'id' => 'create-payment-period',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'data-title' => Module::t('module', 'Create'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container-on-show' => 1,
                        'data-reload-pjax-container' => 'p-jax-workshop-insurance',
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
        <?php Pjax::begin(['id' => 'p-jax-workshop-insurance']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'code',
                'title',
                'manager',
                [
                    'attribute' => 'branch_id',
                    'value' => function (WorkshopInsurance $model) {
                        return $model->branch_id ? $model->branch?->b_name_1 : '';
                    },
                ],
                [
                    'attribute' => 'account_id',
                    'value' => function (WorkshopInsurance $model) {
                        return $model->account_id ? $model->account?->getLink() : '';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created) . '</span>';
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
                    'template' => "{view} {update} {delete} {details}",
                    'buttons' => [
                        'details' => function ($url, $model, $key) {
                            return Html::a('<span class="far fa-list"></span>', ['salary-period/index', 'SalaryPeriodSearch[workshop_id]' => $key], [
                                'title' => Module::t('module', 'Salary Periods'),
                                'class' => 'target'
                            ]);
                        },
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="fa fa-eye text-info"></span>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Details'),
                                    'id' => 'view-ipg-btn',
                                    'data-size' => 'modal-lg',
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
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => 'p-jax-workshop-insurance',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Module::t('module', 'Delete'),
                                    'aria-label' => Module::t('module', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-workshop-insurance',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Module::t('module', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        }
                    ]
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
