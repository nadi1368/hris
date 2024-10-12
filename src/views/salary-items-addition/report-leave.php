<?php

use hesabro\hris\models\SalaryItemsAddition;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $searchModel hesabro\hris\models\SalaryItemsAdditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var array $chartData */

$this->registerJsFile("@web/js/loader.js", ['position' => View::POS_BEGIN]);

$this->title = 'گزارش مرخصی کارمندان';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-items-addition-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title mb-0">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search-report-leave', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body grid-view-without-min-height">
        <?php Pjax::begin(['id' => 'salary-items-addition']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'reloadPjaxContainer' => 'salary-items-addition',
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['class' => SalaryItemsAddition::itemAlias('StatusClass', $model->status)];
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'user_id',
                    'value' => function (SalaryItemsAddition $model, $key, $index, $widget) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'total',
                    'label' => 'مقدار',
                    'value' => function (SalaryItemsAddition $model) {
                        return (int)$model->total . " روز";
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'total_merit_hourly',
                    'label' => 'استحقاقی ساعتی',
                    'value' => function (SalaryItemsAddition $model) {
                        return (int)$model->total_merit_hourly . " ساعت";
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'total_merit_daily',
                    'label' => 'استحقاقی روزانه',
                    'value' => function (SalaryItemsAddition $model) {
                        return (int)$model->total_merit_daily . " روز";
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'total_treatment_daily',
                    'label' => 'استعلاجی روزانه',
                    'value' => function (SalaryItemsAddition $model) {
                        return (int)$model->total_treatment_daily . " روز";
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'total_no_salary_daily',
                    'label' => 'بدون حقوق روزانه',
                    'value' => function (SalaryItemsAddition $model) {
                        return (int)$model->total_no_salary_daily . " روز";
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            $link = [];
                            $link['SalaryItemsAdditionSearch'] = [];
                            $link['SalaryItemsAdditionSearch']['kind'] = [];
                            $link['SalaryItemsAdditionSearch']['user_id'] = $model->user_id;
                            $link['SalaryItemsAdditionSearch']['kind'][] = SalaryItemsAddition::KIND_LEAVE_HOURLY;
                            $link['SalaryItemsAdditionSearch']['kind'][] = SalaryItemsAddition::KIND_LEAVE_DAILY;

                            return Html::a('<span class="far fa-eye text-info"></span>',
                                ArrayHelper::merge(['index'], $link), [
                                    'title' => Yii::t('yii', 'View'),
                                    'class' => 'target'
                                ]);
                        },
                    ]
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
