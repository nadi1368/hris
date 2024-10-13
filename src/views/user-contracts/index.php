<?php

use hesabro\hris\models\UserContracts;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\UserContractsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Contracts');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'user-contracts-p-jax']); ?>
<div class="user-contracts-index card">
	<div class="panel-group m-bot20" id="accordion">
		<div class="card-header d-flex justify-content-between">
			<h4 class="panel-title">
				<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
				   href="#collapseOne" aria-expanded="false">
					<i class="far fa-search"></i> جستجو
				</a>
			</h4>
		</div>
		<div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
			<?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
						return $model->contract?->title;
					},
				],
                [
                    'attribute' => 'branch_id',
                    'value' => function (UserContracts $model) {
                        return $model->branch?->title;
                    },
                ],
				[
					'attribute' => 'user_id',
					'value' => function (UserContracts $model) {
						return $model->user->linkEmployee;
					},
					'format' => 'raw',
				],
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
					'template' => '{print} {confirm} {view} {update} {delete} {change-shelf} {un-confirm} {extending}',
					'buttons' => [
                        'un-confirm' => function ($url, UserContracts $model, $key) {
                            return $model->canUnConfirm() ? Html::a('<span class="fas fa-undo text-danger"></span>',
                                ['user-contracts/un-confirm', 'id' => $model->id],
                            ) : '';
                        },
                        'extending' => function ($url, UserContracts $model, $key) {
                            $startDate = Yii::$app->changeDate->change($model->end_date, 1);
                            return $model->canExtending() ? Html::a('تمدید',
                                ['user-contracts/create', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id, 'contract_id' => $model->contract_id, 'start_date' => $startDate],
                                [
                                    'class' => 'btn btn-success',
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]) : '';
                        },
						'confirm' => function ($url, UserContracts $model, $key) {
							return $model->canConfirm() ? Html::a('تایید نهایی', 'javascript:void(0)',
								[
									'title' => Module::t('module', 'Confirm'),
									'aria-label' => Module::t('module', 'Confirm'),
									'data-reload-pjax-container' => 'user-contracts-p-jax',
									'data-pjax' => '0',
									'data-url' => Url::to(['user-contracts/confirm', 'id' => $model->id]),
									'class' => "btn btn-success p-jax-btn",
									'data-confirm-text' => 'قرارداد از تاریخ ' . $model->start_date . ' تا ' . $model->end_date . ' ... نکته: بعد از تایید نهایی قرارداد قابل ویرایش نمیباشد.',
									'data-confirm-title' => 'تایید نهایی قرارداد ' . $model->user->fullName,
									'data-toggle' => 'tooltip'
								]) : '';
						},
						'update' => function ($url, UserContracts $model, $key) {
							return $model->canUpdate() ? Html::a('<i class="text-success far fa-edit"></i>',
								['user-contracts/update', 'id' => $model->id],
								[
									'class' => 'grid-btn grid-btn-update',
								]) : '';
						},
						'change-shelf' => function ($url, UserContracts $model, $key) {
							return !$model->canUpdate() ? Html::a(
								'<i class="text-success far fa-edit"></i>',
								"javascript:void(0)",
								[
									'id' => 'change-shelf',
									'class' => 'grid-btn grid-btn-update',
									'data-size' => 'modal-lg',
									'data-title' => Module::t('module', 'Update'),
									'data-toggle' => 'modal',
									'data-target' => '#modal-pjax',
									'data-url' => Url::to(['user-contracts/change-shelf', 'id' => $model->id]),
									'data-reload-pjax-container' => 'user-contracts-p-jax',
									'disabled' => true,
								]) : '';
						},
						'view' => function ($url, $model, $key) {
							return Html::a('<i class="text-info far fa-eye"></i>', ['user-contracts/view', 'id' => $model->id], [
								'title' => Module::t('module', 'View'),
								'class' => 'grid-btn grid-btn-view showModalButton',
								'data-size' => 'modal-xl'
							]);
						},
						'delete' => function ($url, $model, $key) use ($searchModel) {
							return $model->canDelete(false) ? Html::a('<span class="fal fa-trash"></span>', ['user-contracts/delete', 'id' => $model->id], [
								'title' => Module::t('module', 'Delete'),
								'class' => 'text-danger',
								'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
								'data-method' => 'post',
							]) : '';
						},
					],
				],
			],
		]); ?>
		<?php Pjax::end(); ?>
	</div>
</div>
