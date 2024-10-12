<?php

use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\OrganizationMemberSearch */
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
				<?= $form->field($model, 'name')->textInput() ?>
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

			<div class="col align-self-center text-right">
				<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>
