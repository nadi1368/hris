<?php

use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\models\SalaryPeriodItemsSearch;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;


/* @var yii\web\View $this */
/* @var SalaryPeriodItemsSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */


$this->title = 'مشاهده فیش های حقوق - ' . $searchModel->user->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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

        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'showFooter' => true,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'period.title',
                [
                    'attribute' => 'hours_of_work',
                    'value' => function($model) {
                        return number_format((float)$model->hours_of_work);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('hours_of_work')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'basic_salary',
                    'value' => function($model) {
                        return number_format((float)$model->basic_salary);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('basic_salary')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cost_of_house',
                    'value' => function($model) {
                        return number_format((float)$model->cost_of_house);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_house')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cost_of_food',
                    'value' => function($model) {
                        return number_format((float)$model->cost_of_food);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_food')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cost_of_children',
                    'value' => function($model) {
                        return number_format((float)$model->cost_of_children);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('cost_of_children')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'rate_of_year',
                    'value' => function($model) {
                        return number_format((float)$model->rate_of_year);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('rate_of_year')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'insurance',
                    'value' => function($model) {
                        return number_format((float)$model->insurance);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('insurance')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'insurance_owner',
                    'value' => function($model) {
                        return number_format((float)$model->insurance_owner);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('insurance_owner')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'tax',
                    'value' => function($model) {
                        return number_format((float)$model->tax);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('tax')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'total_salary',
                    'value' => function($model) {
                        return number_format((float)$model->total_salary);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('total_salary')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'advance_money',
                    'value' => function($model) {
                        return number_format((float)$model->advance_money);
                    },
                    'footer' => number_format((float)$dataProvider->query->sum('advance_money')),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'count_point',
                    'value' => function($model) {
                        return $model->count_point > 0 ? $model->count_point . ' (' . number_format((float)$model->cost_point) . ') ' : null;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'payment_salary',
                    'value' => function($model) {
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
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{print}{view}',
                    'buttons' => [
                        'print' => function($url, SalaryPeriodItems $model, $key) {
                            return $model->canPrint() ? Html::a('<span class="fa fa-print"></span>', ['print-single-item', 'id' => $key], [
                                'title' => Yii::t('app', 'Print'),
                                'class' => 'grid-btn grid-btn-view',
                                'target' => '_blank',
                                'data-pjax' => '0'
                            ]) : '';
                        },
                        'view' => function($url, SalaryPeriodItems $model, $key) {
                            return $model->canPrint() ? Html::a('<span class="fa fa-eye"></span>', ['view-single-item', 'id' => $key], [
                                'title' => Yii::t('app', 'View'),
                                'class' => 'grid-btn grid-btn-view',
                                'data-pjax' => '0'
                            ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
