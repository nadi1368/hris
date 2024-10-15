<?php

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $model EmployeeBranchUser */

?>

<div class="card">
	<div class="panel-group m-bot20" id="accordion">
		<div class="card-header d-flex justify-content-between">
			<div>
				<?= Html::a(Module::t('module','Update Data'),
					'javascript:void(0)', [
						'title' => Module::t('module', 'Insurance Data'),
						'id' => 'insurance-data' . $model->user_id,
						'class' => 'btn btn-primary btn-sm',
						'data-size' => 'modal-xl',
						'data-title' => Module::t('module', 'Insurance Data'),
						'data-toggle' => 'modal',
						'data-target' => '#modal-pjax',
						'data-url' => Url::to(['insurance-data', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
						'data-reload-pjax-container' => "view-user-info-pjax",
					]) ?>

				<?= Html::a('ویرایش اطلاعات حساب',
					'javascript:void(0)', [
						'title' => Module::t('module', 'Update'),
						'id' => 'update-user' . $model->user_id,
						'class' => 'btn btn-success',
						'data-size' => 'modal-xl',
						'data-title' => Module::t('module', 'Update'),
						'data-toggle' => 'modal',
						'data-target' => '#modal-pjax',
						'data-url' => Url::to(['update-user', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
						'data-reload-pjax-container' => "pjax-employee-user",
					]) ?>
			</div>
		</div>
	<div class="card-body">
		<div class="row">
			<?php foreach($model->getInsuranceData() as $attribute => $data): ?>
				<div class="col-md-4 my-3">
					<?= $model->getAttributeLabel($attribute) . ' : ' . '<span class="text-bold">' . $data . '</span>' ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>