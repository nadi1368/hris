<?php

use hesabro\hris\models\EmployeeContentSearch;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EmployeeContentSearch  */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="faq-search">

	<?php $form = ActiveForm::begin([
		'action' => ['public', 'type' => Yii::$app->request->queryParams['type']],
		'method' => 'get',
		'options' => [
			'data-pjax' => 1,
		],
	]); ?>
	<div class="card-body">
		<div class="row">
			<div class="col-md-3">
				<?= $form->field($model, 'title') ?>
			</div>

			<div class="col-12 align-self-center text-right">
				<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>