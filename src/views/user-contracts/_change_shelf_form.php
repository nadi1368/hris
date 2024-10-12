<?php

use hesabro\hris\models\UserContractsShelves;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \hesabro\hris\models\UserContracts */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin([
	'id' => 'change-shelf-form',
]); ?>
<div class="card-body">
	<div class="row">
		<div class="col-md-12">
			<?= $form->field($model, 'shelf_id')->widget(Select2::class, [
				'data' => UserContractsShelves::itemAlias('List'),
				'options' =>
					[
						'placeholder' => Module::t('module', "Search"),
						'dir' => 'rtl',
					],
				'pluginOptions' => [
					'allowClear' => true
				],
			]);
			?>
		</div>
	</div>
</div>
<div class="card-footer">
	<div class="form-group">
		<?= Html::submitButton(Module::t('module', 'Update'), ['class' => 'btn btn-primary btn btn-flat']) ?>
	</div>
</div>
<?php ActiveForm::end(); ?>
