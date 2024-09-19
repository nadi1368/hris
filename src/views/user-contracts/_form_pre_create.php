<?php

use hesabro\hris\models\ContractTemplates;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContracts */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="user-contracts-form">

	<?php $form = ActiveForm::begin([
		'id' => 'form-user-contracts-pre-create',
		'options' => ['data-pjax' => true,]
	]); ?>
	<div class="card-body">
		<div class="row">

			<div class="col-md-12">
				<?= $form->field($model, 'contract_id')->dropdownList(ContractTemplates::itemAlias('ListContract') ?? [], ['prompt' => 'نمونه قرارداد را انتخاب کنید ...']) ?>
			</div>

		</div>
	</div>
	<div class="card-footer">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
