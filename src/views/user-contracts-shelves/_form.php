<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsShelves */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="user-contracts-shelves-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-user-contracts-shelves',
        'options' => ['data-pjax' => true,]
    ]); ?>
    <div class="card-body">
		<div class="row">

			<div class="col-md-6">
				<?= $form->field($model, 'title')->textInput() ?>
			</div>

			<div class="col-md-6">
				<?= $form->field($model, 'capacity')->textInput(['type' => 'number']) ?>
			</div>

		</div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
