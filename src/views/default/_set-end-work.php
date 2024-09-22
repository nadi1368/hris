<?php

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
            <div class="col-md-6">
                <?= $form->field($model, 'end_work')->widget(MaskedInput::className(), ['mask' => '9999/99/99']) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
