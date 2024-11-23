<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchUser */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="deliver-shift-form">
    <?php $form = ActiveForm::begin([
        'id' => 'ajax-form-employee-user'
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-12 date-input">
                <?= $form->field($model, 'end_work')->widget(MaskedInput::class, ['mask' => '9999/99/99']) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'settlement_leave')->checkbox()->label($model->getAttributeLabel('settlement_leave') . ' انجام شده است.') ?>
                <?= $form->field($model, 'settlement_loans')->checkbox()->label($model->getAttributeLabel('settlement_loans') . ' انجام شده است.') ?>
                <?= $form->field($model, 'settlement_comforts')->checkbox()->label($model->getAttributeLabel('settlement_comforts') . ' انجام شده است.') ?>
                <?= $form->field($model, 'settlement_insurance_addition')->checkbox()->label($model->getAttributeLabel('settlement_insurance_addition') . ' انجام شده است.') ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
