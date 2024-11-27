<?php

use hesabro\hris\models\AdvanceMoneySearch;
use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel AdvanceMoneySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Advance Money');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'pjax-advance-money']) ?>
<div class="advance-money-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
            <?php if(Yii::$app->user->can('EmployeeBranch/index')):?>
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            <?php endif;?>
            </h4>
            <div>
                <?= Html::button(Yii::t('app', 'Create'),
                    $searchModel->canCreate() ? [
                        'title' => Module::t('module', 'Create'),
                        'id' => 'create-advance-money',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-xl',
                        'data-title' => Module::t('module', 'Create'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['employee-advance-money/create']),
                        'data-reload-pjax-container-on-show' => 1,
                        'data-reload-pjax-container' => "pjax-advance-money",
                        'data-handle-form-submit' => 1,
                    ] :
                    [
                        'class' => 'btn btn-success',
                        'title' => $searchModel->error_msg,
                        'disabled' => 1,
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
            //'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
                return [
                    'class'=>AdvanceMoney::itemAlias('StatusClass',$model->status)
                ];
            },
            'columns' => [
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) use($dataProvider) {
                        return $this->render('_index', [
                            'model' => $model,
                            'dataProvider' => $dataProvider
                        ]);
                    },
                ],
                ['class' => 'yii\grid\SerialColumn'],

                'amount:currency',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return AdvanceMoney::itemAlias('Status', $model->status);
                    },
                    'filter' => AdvanceMoney::itemAlias('Status')
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw'
                ],

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a('<span class="far fa-trash-alt text-danger"></span>', Url::to(['delete', 'id' => $key]), [
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'class' => 'ajax-btn',
                                'data-view' => 'index',
                                'data-p-jax' => '#pjax-advance-money',
                            ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>
