<?php

use hesabro\hris\models\EmployeeContent;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \hesabro\hris\models\EmployeeContentSearch */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var bool $isTypeSet */
?>

<div class="faq-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
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

			<div class="col-md-5">
				<?= $form->field($model, 'description') ?>
			</div>

			<div class="col-md-2">
				<?= $form->field($model, 'type')->dropdownList(EmployeeContent::itemAlias('Type'), ['prompt' => Module::t('module', 'Select...')]) ?>
			</div>

			<?php if(Yii::$app->client->isMaster()) :?>
            <div class="col-md-2">
                <?php echo $form->field($model, 'ignore_client')->checkbox()->label(Module::t('module', 'Ignore Client')) ?>
            </div>
			<?php endif; ?>

			<div class="col-12 align-self-center text-right">
				<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
				<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>