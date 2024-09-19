<?php

use hesabro\hris\models\UserContractsShelves;
use common\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\UserContractsShelvesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User Contracts Shelves');
$this->params['breadcrumbs'][] = $this->title;
?>
    <?php Pjax::begin(['id' => 'user-contracts-shelves-p-jax']); ?>
<div class="user-contracts-shelves-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                                    <?= Html::a('<button class="btn btn-circle btn-success text-white"> <i class="fal fa-plus"></i></button>',
                    "javascript:void(0)",
                    [
                    'id' => 'user-contracts-shelves-create',
                    'class' => 'grid-btn grid-btn-update',
                    'data-size' => 'modal-lg',
                    'data-title' => Yii::t('app', 'Create'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['create']),
                    'data-reload-pjax-container' => 'user-contracts-shelves-p-jax',
                    'disabled' => true
                    ]); ?>
                            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            			    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
			        </div>
    </div>
    <div class="card-body">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			[
				'attribute' => 'title',
				'value' => function (UserContractsShelves $model) {
					return Html::a($model->title, ['view', 'id' => $model->id]);
				},
				'format' => 'raw'
			],
			'capacity',
			[
				'attribute' => 'active_contracts_count',
				'value' => function (UserContractsShelves $model) {
					return $model->getActiveContracts()->count();
				}
			],

			['class' => 'common\widgets\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                                                return Html::a('<i class="text-success far fa-edit"></i>',
                        "javascript:void(0)",
                        [
                        'id' => 'user-contracts-shelves-update',
                        'class' => 'grid-btn grid-btn-update',
                        'data-size' => 'modal-lg',
                        'data-title' => Yii::t('app', 'Update'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['update', 'id' => $model->id]),
                        'data-reload-pjax-container' => 'user-contracts-shelves-p-jax',
                        'disabled' => true,
                        ]);
                                             },
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="text-info far fa-eye"></i>', ['view', 'id' => $model->id], [
                        'title' => Yii::t('app', 'View'),
                        'class' => 'grid-btn grid-btn-view'
                        ]);
                    },
                ],
            ],
		],
	]); ?>
    <?php Pjax::end(); ?>
	</div>
</div>
