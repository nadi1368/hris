<?php

use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;
use hesabro\hris\models\RateOfYearSalary;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\RateOfYearSalarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rate Of Year Salaries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rate-of-year-salary-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(Yii::t('app', 'Create'),
                    'javascript:void(0)', [
                        'title' => Yii::t('app', 'Create'),
                        'data-title' => Yii::t('app', 'Create'),
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container-on-show' => 0,
                        'data-reload-pjax-container' => 'p-jax-rate-of-year',
                    ]); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'p-jax-rate-of-year']
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'year',
                'rate_of_day:currency',
                [
                    'attribute' => 'created_at',
                    'value' => function (RateOfYearSalary $model) {
                        return '<span title="بروز رسانی شده در '.Yii::$app->jdate->date("Y/m/d  H:i", $model->updated_at).'">'.Yii::$app->jdate->date("Y/m/d  H:i", $model->created_at).'</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function (RateOfYearSalary $model) {
                        return '<span title="بروز رسانی شده توسط '.$model->update?->fullName.'">'.$model->creator?->fullName.'</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, RateOfYearSalary $model, $key) {
                            $items = [];

                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Yii::t('app', 'Update'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-pjax' => '0',
                                    'title' => Yii::t('app', 'Update'),
                                    'data-title' => Yii::t('app', 'Update'),
                                    'data-size' => 'modal-lg',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $key]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'p-jax-rate-of-year',
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Yii::t('app', 'Delete'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'title' => Yii::t('app', 'Delete'),
                                    'aria-label' => Yii::t('app', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-rate-of-year',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => "text-danger p-jax-btn",
                                    'data-title' => Yii::t('app', 'Delete'),
                                    'data-method' => 'post'
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Yii::t('app', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Yii::t('app', 'Log'),
                                    'class' => 'showModalButton',
                                    'data-size' => 'modal-xxl',
                                ],
                            ];

                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Yii::t('app', 'Actions')],
                                'encodeLabel' => false,
                                'label' => '<i class="far fa-list mr-1"></i>',
                                'options' => ['class' => 'float-right'],
                                'dropdown' => [
                                    'items' => $items,
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
