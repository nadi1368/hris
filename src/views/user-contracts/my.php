<?php

use hesabro\hris\models\UserContracts;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchUser */
/* @var $searchModel hesabro\hris\models\UserContractsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->user->fullName;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'user-data-pjax', 'enablePushState' => false]); ?>
<div class="user-contracts-index card">
	<div class="card-header">
		<?= $this->render('/employee-branch/_view_user_nav', [
			'model' => $model,
		]) ?>
	</div>

	<div class="panel-group m-bot20" id="accordion">
		<div class="card-header d-flex justify-content-between">
			<!--            <h4 class="panel-title">-->
			<!--                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"-->
			<!--                   href="#collapseOne" aria-expanded="false">-->
			<!--                    <i class="far fa-search"></i> جستجو-->
			<!--                </a>-->
			<!--            </h4>-->
			<div>
				<?= Html::a('ایجاد قرارداد برای کارمند',
					"javascript:void(0)",
					[
						'id' => 'user-contracts-create',
						'class' => 'btn btn-success',
						'data-size' => 'modal-lg',
						'data-title' => Module::t('module', 'Create'),
						'data-toggle' => 'modal',
						'data-target' => '#modal-pjax-over',
						'data-url' => Url::to(['pre-create', 'user_id' => $model->user_id, 'branch_id' => $model->branch_id]),
						//'data-reload-pjax-container' => 'user-contracts-p-jax',
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
				[
					'attribute' => 'shelf_id',
					'value' => function (UserContracts $model) {
						return $model->shelf->title ?? null;
					},
				],


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
</div>
<?php Pjax::end(); ?>
