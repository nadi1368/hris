<?php

use hesabro\hris\models\UserContracts;
use common\widgets\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var hesabro\hris\models\UserContractsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'My Contracts');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'user-contracts-p-jax']); ?>
<div class="user-contracts-index card">
	<div class="card-body">
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'columns' => [
				['class' => 'yii\grid\SerialColumn'],
				[
					'attribute' => 'contract_id',
					'value' => function (UserContracts $model) {
						return $model->contract->title;
					},
				],
				'start_date',
				'end_date',
				'month',
				[
					'class' => 'common\widgets\grid\ActionColumn',
					'template' => '{view}',
					'buttons' => [
						'view' => function ($url, $model, $key) {
							return Html::a('<i class="text-info far fa-eye"></i>', ['user-contracts/view-my-contract', 'id' => $model->id], [
								'title' => Yii::t('app', 'View'),
								'class' => 'grid-btn grid-btn-view showModalButton',
								'data-size' => 'modal-xl'
							]);
						},
					],
				],
			],
		]); ?>
		<?php Pjax::end(); ?>
	</div>
</div>