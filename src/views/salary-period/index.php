<?php

use hesabro\hris\models\SalaryPeriod;
use common\models\Year;
use common\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\SalaryPeriodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Salary Periods');
if ($searchModel->workshop_id) {
    $this->params['breadcrumbs'][] = ['label' => $searchModel->workshop->fullName, 'url' => ['workshop-insurance/index', 'WorkshopInsuranceSearch[id]' => $searchModel->workshop_id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-period-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= $searchModel->workshop_id && $searchModel->workshop->canCreateYear() ? Html::a('ایجاد سنوات '.Year::getDefault('title'),
                    ['create-year', 'workshop_id' => $searchModel->workshop_id], [
                        'data-confirm' => Yii::t('app', 'Are you sure?'),
                        'data-method' => 'post',
                        'class' => 'btn btn-success',
                    ]) : ''; ?>
                <?= $searchModel->workshop_id && $searchModel->workshop->canCreateReward() ? Html::a('ایجاد عیدی و پاداش',
                    ['create-reward', 'workshop_id' => $searchModel->workshop_id], [
                        'data-confirm' => Yii::t('app', 'Are you sure?'),
                        'data-method' => 'post',
                        'class' => 'btn btn-success',
                    ]) : ''; ?>
                <?= $searchModel->workshop_id ? ($searchModel->workshop->canCreateSalary() ? Html::a('ایجاد دوره حقوق',
                    'javascript:void(0)', [
                        'title' => 'ایجاد دوره حقوق',
                        'id' => 'create-payment-period',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'data-title' => 'ایجاد دوره حقوق',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create', 'workshop_id' => $searchModel->workshop_id]),
                        'data-reload-pjax-container-on-show' => 1,
                        'data-reload-pjax-container' => 'p-jax-salary-period',
                        'data-handleFormSubmit' => 1,
                        'disabled' => true
                    ]) : Html::a(
                    'ایجاد دوره حقوق',
                    'javascript:void(0)',
                    [
                        'data-pjax' => '0',
                        'class' => "btn btn-secondary alert-btn ml-1",
                        //'data-alert-title' => 'post',
                        'data-alert-text' => $searchModel->workshop->error_msq,
                    ])
                ) : ''; ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <div class="card-body">
        <?= $this->render('_nav', [
            'searchModel' => $searchModel,
        ]); ?>
    </div>
    <div class="card-body">
        <?php Pjax::begin(['id' => 'p-jax-salary-period']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'title',
                [
                    'attribute' => 'workshop_id',
                    'value' => function ($model) {
                        return $model->workshop->fullName;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'start_date',
                    'value' => function ($model) {
                        return Yii::$app->jdate->date("Y/m/d", $model->start_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'end_date',
                    'value' => function ($model) {
                        return Yii::$app->jdate->date("Y/m/d", $model->end_date);
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
					'attribute' => 'status',
					'value' => function (SalaryPeriod $model) {
						return Html::tag('span', SalaryPeriod::itemAlias('Status', $model->status), ['class' => 'badge badge-' . SalaryPeriod::itemAlias('StatusColor', $model->status) . ' font-bold']);
					},
					'format' => 'raw'
				],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    //'template' => "{delete} {update}",
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return
                                Html::a('<span class="fa fa-eye text-info"></span>',
                                    [SalaryPeriod::itemAlias('KindLink', (int)$model->kind), 'id' => $key], [
                                        'title' => Yii::t('app', 'Details'),
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
                                    'data-reload-pjax-container' => 'p-jax-salary-period',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-salary-period',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Yii::t('yii', 'Delete'),
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
