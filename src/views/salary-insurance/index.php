<?php

use hesabro\hris\models\SalaryInsurance;
use common\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\SalaryInsuranceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Salary Insurances');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-insurance-index card">
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
                        'id' => 'create-payment-period',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'data-title' => Yii::t('app', 'Create'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container-on-show' => 1,
                        'data-reload-pjax-container' => 'p-jax-salary-insurance',
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
        <?php Pjax::begin(['id'=>'p-jax-salary-insurance']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'code',
                'group',
                [
                    'attribute' => 'tag_id',
                    'value' => function (SalaryInsurance $model) {
                        return $model->tag?->title ?: '';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'creator_id',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update?->fullName . '">' . $model->creator?->fullName . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    //'template' => "{view} {update}",
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return
                                Html::a('<span class="fa fa-eye text-info"></span>',
                                    'javascript:void(0)', [
                                        'title' => Yii::t('app', 'Details'),
                                        'id' => 'view-ipg-btn',
                                        'data-size' => 'modal-lg',
                                        'data-title' => Yii::t('app', 'Details'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['view', 'id' => $model->id]),
                                        'data-action' => 'view-ipg',
                                        'data-handleFormSubmit' => 0,
                                        'disabled' => true
                                    ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="fa fa-edit text-primary"></span>', 'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Update'),
                                    'id' => 'edit-ipg-btn',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Yii::t('app', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => 'p-jax-salary-insurance',
                                    'data-handleFormSubmit' => 1,
                                    'disabled' => true
                                ]) : '';
                        },
                        'delete' => function ($url,SalaryInsurance $model, $key) {
                            return $model->canDelete() ?Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-workshop-insurance',
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
        <?php Pjax::end() ?>
    </div>
</div>
