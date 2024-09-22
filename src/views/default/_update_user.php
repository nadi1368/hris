<?php

use hesabro\hris\models\EmployeeBranch;
use hesabro\hris\models\EmployeeBranchUser;
use common\models\Account;
use yii\helpers\ArrayHelper;
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
            <div class="col-md-2">
                <?= $form->field($model, "branch_id")->dropDownList(EmployeeBranch::itemAlias('List')) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "salary")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'shaba')->widget(MaskedInput::class, [
                    'mask' => 'IR99-9999-9999-9999-9999-9999-99',
                    'options' => ['class' => 'form-control ltr'],
                    'clientOptions' => [
                        'removeMaskOnSubmit' => true,
                    ]
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'shaba_non_cash')->widget(MaskedInput::class, [
                    'mask' => '9999-9999-9999-9999',
                    'options' => ['class' => 'form-control ltr'],
                    'clientOptions' => [
                        'removeMaskOnSubmit' => true,
                    ]
                ]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'account_non_cash') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'shift')->dropDownList(EmployeeBranchUser::itemAlias('Shift'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>
            <div class='col-md-3'>
                <?= $form->field($model, 'roll_call_id') ?>
            </div>
            <div class='col-md-3'>
                <?= $form->field($model, 'account_id')->dropdownList(ArrayHelper::map($model->user->customer->account,'id', 'fullName'), ['prompt' => Yii::t('app', 'Select...')]) // TODO: What To Do ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, "delete_point")->checkbox() ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, "manager")->checkbox() ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, "confirmed")->checkbox([ 'checked' => $model->isConfirmed ]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
