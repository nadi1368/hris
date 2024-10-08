<?php

use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\UserContractsSearch;
use common\models\User;
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
				<?= $form->field($model, 'contract_id')->dropdownList(ContractTemplates::itemAlias('ListContract') ?? [], ['prompt' => Yii::t('app', 'Select...')]) ?>
			</div>

			<div class="col-md-4">
				<?= $form->field($model, 'user_id')->widget(Select2::class, [
					'data' => User::getUserWithRoles(['employee']),
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => Yii::t('app', 'Search'),
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
				<?= $form->field($model, 'contract_status')->dropdownList(UserContractsSearch::itemAlias('contract_status'), ['prompt' => Yii::t('app', 'Select...')]) ?>
			</div>

			<div class="col align-self-center text-right">
				<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary']) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>
