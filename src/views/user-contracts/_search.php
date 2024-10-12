<?php

use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\UserContractsSearch;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="user-contracts-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'options' => [
			'data-pjax' => 1,
		],
	]); ?>
	<div class="card-body">
		<div class="row">

			<div class="col-md-4">
				<?= $form->field($model, 'contract_id')->dropdownList(ContractTemplates::itemAlias('ListContract') ?? [], ['prompt' => Module::t('module', 'Select...')]) ?>
			</div>

			<div class="col-md-4">
				<?= $form->field($model, 'user_id')->widget(Select2::class, [
					'data' => Module::getInstance()->user::getUserWithRoles(['employee']),
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => Module::t('module', 'Search'),
						'dir' => 'rtl',
					],
				]); ?>
			</div>

			<div class="col-md-2 date-input">
				<?= $form->field($model, 'fromStartDate')->widget(MaskedInput::class, [
                'mask' => '9999/99/99',
            ]) ?>
			</div>

			<div class="col-md-2 date-input">
				<?= $form->field($model, 'toStartDate')->widget(MaskedInput::class, [
                'mask' => '9999/99/99',
            ]) ?>
			</div>

			<div class="col-md-2 date-input">
				<?= $form->field($model, 'fromEndDate')->widget(MaskedInput::class, [
					'mask' => '9999/99/99',
				]) ?>
			</div>

			<div class="col-md-2 date-input">
				<?= $form->field($model, 'toEndDate')->widget(MaskedInput::class, [
					'mask' => '9999/99/99',
				]) ?>
			</div>

			<div class="col-md-3">
				<?= $form->field($model, 'contract_status')->dropdownList(UserContractsSearch::itemAlias('contract_status'), ['prompt' => Module::t('module', 'Select...')]) ?>
			</div>

			<div class="col align-self-center text-right">
				<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>
