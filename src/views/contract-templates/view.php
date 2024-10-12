<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ContractTemplates */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', "$model->typeText Templates"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-templates-view card">
	<div class="card-body">
		<?= TableView::widget([
			'model' => $model,
			'attributes' => [
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
						return Yii::$app->jdf->jdate("Y/m/d H:i:s", $model->created_at);
					},
				],
			],
		]) ?>

        <div>
            <?= $model->description ?: '' ?>
        </div>


        <?php if (is_array($model->clausesModels) && count($model->clausesModels)):?>
            <div class="row">
                <div class="col-md-12 my-3 text-center" style="border-bottom: 1px solid rgba(0,0,0,0.2);">
                    <h2><?= Module::t('module', "$model->typeText Clauses") ?></h2>
                </div>
                <?php foreach ($model->clausesModels as $clause): ?>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><?= $clause->title ?></h3>
                            </div>
                            <div class="card-body">
                                <?= nl2br($clause->description) ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div>
            <?= $model->signatures ?: '' ?>
        </div>
	</div>
	<div class="card-footer">
		<?php // Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</div>
</div>
