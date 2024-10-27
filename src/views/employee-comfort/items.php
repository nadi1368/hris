<?php
use hesabro\hris\models\ComfortItems;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\employee\models\ComfortItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'My Requests');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Profile'), 'url' => ['/profile/index']];
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Comforts'), 'url' => ['/comfort/index']];
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin(['id' => 'pjax-comfort-items']);
?>

<div class="comfort-items-index card">
    <div class='card-header d-flex align-items-center justify-content-start'>
        <a class="accordion-toggle collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="false">
            <i class="far fa-search"></i> جستجو
        </a>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false"></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['class' => ComfortItems::itemAlias('StatusClass', $model->status)];
            },
            //'filterModel' => $searchModelItems,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'comfort_id',
                    'value' => function (ComfortItems $model) {
                        return $model->comfort->title;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function (ComfortItems $model) {
                        return ComfortItems::itemAlias('Status', $model->status);
                    },
                    'format' => 'raw'
                ],
                'amount:currency',
                [
                    'attribute' => 'created',
                    'value' => function (ComfortItems $model) {
                        return Yii::$app->jdate->date('Y/m/d', $model->created);
                    },
                    'format' => 'raw'
                ],
                //'attach',
                //'description:ntext',
                //'additional_data',
                //'status',
                //'creator_id',
                //'update_id',
                //'changed',
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{view}{delete}",
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="fa fa-eye text-info"></span>',
                                'javascript:void(0)', [
                                    'title' => Module::t('module', 'Details'),
                                    'id' => 'view-ipg-btn',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Details'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['view-items', 'id' => $model->id]),
                                    'data-action' => 'view-ipg',
                                    'data-handleFormSubmit' => 0,
                                    'disabled' => true
                                ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'pjax-comfort-items',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Yii::t('yii', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>

<?php Pjax::end() ?>
