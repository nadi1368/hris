<?php

use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\YearSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Salary Years Settings');
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
                                        'title' => Yii::t('app', 'Salary Bases'),
                                        'id' => 'edit-ipg-btn',
                                        'data-size' => 'modal-xxl',
                                        'data-title' => Yii::t('app', 'Salary Bases') . ' - ' . $model->title,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['default/update-year-setting', 'id' => $model->id]),
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
