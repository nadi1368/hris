<?php

use hesabro\hris\models\EmployeeBranchUser;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $model EmployeeBranchUser */

?>

<div class="card">
	<div class="panel-group m-bot20" id="accordion">
		<div class="card-header d-flex justify-content-between">
			<div>
				<?= Html::a(Yii::t('app','Update Data'),
					'javascript:void(0)', [
						'title' => Yii::t('app', 'Insurance Data'),
						'id' => 'insurance-data' . $model->user_id,
						'class' => 'btn btn-primary btn-sm',
						'data-size' => 'modal-xl',
						'data-title' => Yii::t('app', 'Insurance Data'),
						'data-toggle' => 'modal',
						'data-target' => '#modal-pjax',
						'data-url' => Url::to(['insurance-data', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
						'data-reload-pjax-container-on-show' => 1,
						'data-reload-pjax-container' => "view-user-info-pjax",
						'data-handle-form-submit' => 1,
						'disabled' => true,
					]) ?>

				<?= Html::a('ویرایش اطلاعات حساب',
					'javascript:void(0)', [
						'title' => Yii::t('app', 'Update'),
						'id' => 'update-user' . $model->user_id,
						'class' => 'btn btn-success',
						'data-size' => 'modal-xl',
						'data-title' => Yii::t('app', 'Update'),
						'data-toggle' => 'modal',
						'data-target' => '#modal-pjax',
						'data-url' => Url::to(['update-user', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
						'data-reload-pjax-container-on-show' => 1,
						'data-reload-pjax-container' => "pjax-employee-user",
						'data-handle-form-submit' => 1,
						'disabled' => true,
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