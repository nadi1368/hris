<?php

use hesabro\hris\Module;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;
use hesabro\hris\models\RateOfYearSalary;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\RateOfYearSalarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Rate Of Year Salaries');
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
                <?= Html::a(Module::t('module', 'Create'),
                    'javascript:void(0)', [
                        'title' => Module::t('module', 'Create'),
                        'data-title' => Module::t('module', 'Create'),
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
                        return '<span title="بروز رسانی شده در '.Yii::$app->jdf->jdate("Y/m/d  H:i", $model->updated_at).'">'.Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created_at).'</span>';
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
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-pjax' => '0',
                                    'title' => Module::t('module', 'Update'),
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-size' => 'modal-lg',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $key]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'p-jax-rate-of-year',
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Module::t('module', 'Delete'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                    'title' => Module::t('module', 'Delete'),
                                    'aria-label' => Module::t('module', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-rate-of-year',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => "text-danger p-jax-btn",
                                    'data-title' => Module::t('module', 'Delete'),
                                    'data-method' => 'post'
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Module::t('module', 'Log'),
                                'url' => ['/change-log/default/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
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
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
