<?php

use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ContractTemplatesSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="contract-templates-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'options' => [
			'data-pjax' => 1,
		],
	]); ?>
	<div class="card-body">
		<div class="row">

			<div class="col-md-2">
				<?= $form->field($model, 'title') ?>
			</div>

			<div class="col-md-2">
				<?= $form->field($model, 'description') ?>
			</div>

			<div class="col align-self-center text-right">
				<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>
