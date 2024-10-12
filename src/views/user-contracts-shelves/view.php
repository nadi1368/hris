<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsShelves */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'User Contracts Shelves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contracts-shelves-view card">
	<div class="card-body">
		<?= TableView::widget([
			'model' => $model,
			'attributes' => [
				'id',
				'title',
				'capacity',
				[
					'attribute' => 'created_by',
					'value' => $model->creator->fullName,
				],
				'created_at:datetime',
			],
		]) ?>

		<?= $this->render('/user-contracts/index', [
			'searchModel' => $contractsSearchModel,
			'dataProvider' => $contractsDataProvider,
		]) ?>
	</div>
	<div class="card-footer">

	</div>
</div>
