<?php


use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\models\UserContracts;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */
?>


<div class="card-body">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			//'id',
			[
				'attribute' => 'contract_id',
				'value' => function (UserContracts $model) {
					return $model->contract->title;
				},
			],
			//'user_id',
			'start_date',
			'end_date',
			'month',


			['class' => 'common\widgets\grid\ActionColumn',
				'template' => '{confirm} {view} {update} {delete}',
				'buttons' => [
					'confirm' => function ($url, UserContracts $model, $key) {
						return $model->canConfirm() ? Html::a('تایید نهایی', 'javascript:void(0)',
							[
								'title' => Module::t('module', 'Confirm'),
								'aria-label' => Module::t('module', 'Confirm'),
								'data-reload-pjax-container' => 'user-contracts-p-jax',
								'data-pjax' => '0',
								'data-url' => Url::to(['confirm', 'id' => $model->id]),
								'class' => "btn btn-success p-jax-btn",
								'data-confirm-text' => 'قرارداد از تاریخ ' . $model->start_date . ' تا ' . $model->end_date . ' ... نکته: بعد از تایید نهایی قرارداد قابل ویرایش نمیباشد.',
								'data-confirm-title' => 'تایید نهایی قرارداد ' . $model->user->fullName,
								'data-toggle' => 'tooltip'
							]) : '';
					},
					'update' => function ($url, UserContracts $model, $key) {
						return $model->canUpdate() ? Html::a('<i class="text-success far fa-edit"></i>',
							['update', 'id' => $model->id],
							[
								'class' => 'grid-btn grid-btn-update',
							]) : '';
					},
					'view' => function ($url, $model, $key) {
						return Html::a('<i class="text-info far fa-eye"></i>', ['view', 'id' => $model->id], [
							'title' => Module::t('module', 'View'),
							'class' => 'grid-btn grid-btn-view showModalButton',
							'data-size' => 'modal-xl'
						]);
					},
				],
			],
		],
	]); ?>
</div>
