<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel object */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Salary Years Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-body">
        <?php Pjax::begin() ?>
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'title',
                'start',
                'end',
                //'status',
                //'created',
                //'changed',


                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{salaryJson}',
                    'buttons' => [
                        'salaryJson' => function ($url, $model, $key) {
                            return
                                Html::a('<span class="fa fa-edit text-primary"></span>',
                                    'javascript:void(0)', [
                                        'title' => Module::t('module', 'Salary Bases'),
                                        'id' => 'edit-ipg-btn',
                                        'data-size' => 'modal-xxl',
                                        'data-title' => Module::t('module', 'Salary Bases') . ' - ' . $model->title,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['employee-branch/update-year-setting', 'id' => $model->id]),
                                        'data-action' => 'edit-ipg',
                                        //'data-reload-pjax-container' => 'p-jax-ipg',
                                        'data-handleFormSubmit' => 1,
                                        'disabled' => true
                                    ]);
                        },
                    ]
                ],
            ],
        ]); ?>
        <?php Pjax::end() ?>
    </div>
</div>
