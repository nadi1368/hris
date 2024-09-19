<?php

use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\UserContracts;
use hesabro\hris\models\UserContractsShelves;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContracts */
/* @var $modelUser EmployeeBranchUser */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="user-contracts-form">

	<?php $form = ActiveForm::begin([
		'id' => 'form-user-contracts',
		'options' => ['data-pjax' => true,],
	]); ?>
	<div class="card-body">
		<div class="row">

			<div class="col-md-6">
				<?= $form->field($model, 'contract_id')->dropdownList(ContractTemplates::itemAlias('ListContract'), ['disabled' => true]) ?>
			</div>

			<div class="col-md-6">
				<?= $form->field($model, 'user_id')->textInput(['disabled' => true, 'value' => $model->user->fullName]) ?>
			</div>

			<div class="col-md-4">
				<?= $form->field($model, 'start_date')->widget(
					MaskedInput::className(), [
					'mask' => '9999/99/99',
				]) ?>
			</div>

			<div class="col-md-4">
				<?= $form->field($model, 'end_date')->widget(
					MaskedInput::className(), [
					'mask' => '9999/99/99',
				]) ?>
			</div>

			<div class="col-md-4">
				<?= $form->field($model, 'month')->textInput() ?>
			</div>

			<div class="col-md-3">
				<?= $form->field($model, 'daily_salary')->widget(MaskedInput::className(),
                    [
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => 'form-control mask_currency',
                        ],
                        'clientOptions' => [
                            'alias' => 'integer',
                            'groupSeparator' => ',',
                            'autoGroup' => true,
                            'removeMaskOnSubmit' => false,
                            'autoUnmask' => false,
                        ],
                    ]) ?>
			</div>

			<div class="col-md-3">
				<?= $form->field($model, 'right_to_housing')->widget(MaskedInput::className(),
                    [
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => 'form-control mask_currency',
                        ],
                        'clientOptions' => [
                            'alias' => 'integer',
                            'groupSeparator' => ',',
                            'autoGroup' => true,
							'removeMaskOnSubmit' => false,
							'autoUnmask' => false,
                        ],
                    ]) ?>
			</div>

			<div class="col-md-3">
				<?= $form->field($model, 'right_to_food')->widget(MaskedInput::className(),
                    [
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => 'form-control mask_currency',
                        ],
                        'clientOptions' => [
                            'alias' => 'integer',
                            'groupSeparator' => ',',
                            'autoGroup' => true,
							'removeMaskOnSubmit' => false,
							'autoUnmask' => false,
                        ],
                    ]) ?>
			</div>

			<div class="col-md-3">
				<?= $form->field($model, 'right_to_child')->widget(MaskedInput::className(),
					[
						'options' => [
							'autocomplete' => 'off',
							'class' => 'form-control mask_currency',
						],
						'clientOptions' => [
							'alias' => 'integer',
							'groupSeparator' => ',',
							'autoGroup' => true,
							'removeMaskOnSubmit' => false,
							'autoUnmask' => false,
						],
					]) ?>
			</div>

			<div class="col-md-4">
				<?= $form->field($model, 'shelf_id')->widget(Select2::class, [
					'data' => UserContractsShelves::itemAlias('ListEmpty'),
					'options' => [
						'placeholder' => Yii::t('app', 'Select...'),
						'dir' => 'rtl',
					],
				]); ?>
			</div>

			<div class="col-md-12">
				<hr>
				<h3>متغیر های قرارداد (تمام متغیر ها باید تکمیل شوند)</h3>
			</div>


			<?php
			$staticVariables = UserContracts::itemAlias('ContractStaticVariables');
			$userStaticVariables = EmployeeBranchUser::itemAlias('insuranceDataDefaultVariables');
			$insuranceData = $modelUser->getInsuranceData(true);
			foreach ($model->contract->variables as $variable => $variableTitle):
				$variableTitle = array_key_exists($variable, $userStaticVariables) ? $userStaticVariables[$variable] : $variableTitle;

				if (!array_key_exists($variable, $staticVariables)): ?>

					<div class="col-md-3">
						<div class="form-group">
							<label for="usercontracts-variables-<?= $variable ?>"><?= $variableTitle  . ' (' . $variable . ')' ?></label>
							<input type="text" class="form-control '+inputClass+'"
								   id="usercontracts-variables-<?= $variable ?>"
								   name="UserContracts[variables][<?= $variable ?>]"
								   value="<?= isset($model->variables[$variable]) ? $model->variables[$variable] : (array_key_exists($variable, $userStaticVariables) ? $insuranceData[$variable] : null) ?>">
							<div class="invalid-feedback"></div>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

		</div>
	</div>
	<div class="card-footer">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
