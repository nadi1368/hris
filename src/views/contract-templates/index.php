<?php

use hesabro\hris\models\ContractTemplates;
use common\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
/**
 * @var $this yii\web\View
 * @var $searchModel hesabro\hris\models\ContractTemplatesSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', "$searchModel->typeText Templates");
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'contract-templates-p-jax']); ?>
<div class="contract-templates-index card">
	<div class="panel-group m-bot20" id="accordion">
		<div class="card-header d-flex justify-content-between">
			<h4 class="panel-title">
				<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
				   href="#collapseOne" aria-expanded="false">
					<i class="far fa-search"></i> جستجو
				</a>
			</h4>
			<div>
				<?= Html::a(Yii::t('app','Create'), Url::to(['create', 'type' => $searchModel->type ]),['class'=>'btn btn-success text-white']); ?>

                <?= Html::a(Yii::t('app','Import Json'), 'javascript:void(0)', [
                    'class' => 'btn btn-info',
                    'data-size' => 'modal-md',
                    'data-title' => Yii::t('app','Import Json'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['json-import']),
                    'data-reload-pjax-container' => 'contract-templates-p-jax',
                    'disabled' => true
                ]); ?>
			</div>
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
				[
					'class' => 'kartik\grid\ExpandRowColumn',
					'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
					'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
					'value' => function ($model, $key, $index, $column) {
						return GridView::ROW_COLLAPSED;
					},
					'detail' => function ($model, $key, $index, $column) {
						return Yii::$app->controller->renderPartial('_index', [
							'model' => $model,
						]);
					},
				],

				'title:ntext',
				//'description:ntext',
				[
					"attribute" => "created_by",
					"value" => function ($model) {
						$data = $model->creator ? $model->creator->fullName : '';

						return $data;
					},
				],
				[
					"attribute" => "created_at",
					"value" => function ($model) {
						return Yii::$app->jdate->date("Y/m/d H:i:s", $model->created_at);
					},
				],

				['class' => 'common\widgets\grid\ActionColumn',
					'template' => '{view} {update} {delete} {copy} {export}',
					'buttons' => [
                        'export' => function ($url, $model, $key) {
                            return Html::a('<i class="text-primary far fa-file-export"></i>',
                                Url::to(['json-export', 'contract_id' => $model->id]),
                                [
                                    'class' => 'grid-btn grid-btn-update',
                                    'data-pjax' => 0
                                ],
                            );
                        },
						'copy' => function ($url, $model, $key) {
							return Html::a('<i class="text-success far fa-copy"></i>',
								Url::to(['create', 'copy_contract_id' => $model->id]),
								[
									'class' => 'grid-btn grid-btn-update',
								],
							);
						},
						'update' => function ($url, $model, $key) {
							return Html::a('<i class="text-success far fa-edit"></i>',
								Url::to(['update', 'id' => $model->id]),
								[
									'class' => 'grid-btn grid-btn-update',
								],
							);
						},
						'view' => function ($url, $model, $key) {
							return Html::a('<i class="text-info far fa-eye"></i>', ['view', 'id' => $model->id], [
								'title' => Yii::t('app', 'View'),
								'class' => 'grid-btn grid-btn-view showModalButton',
								'data-size' => 'modal-xl',
							]);
						},
						'delete' => function ($url, ContractTemplates $model, $key) use ($searchModel) {
							return $model->canDelete() ? Html::a('<span class="fal fa-trash"></span>', ['delete', 'id' => $model->id], [
								'title' => Yii::t('yii', 'Delete'),
								'class' => 'text-danger',
								'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
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
