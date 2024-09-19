<?php

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\SalaryInsurance;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
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
            <div class="col-md-3">
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'father_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nationalCode')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'national')->dropDownList(\common\models\Customer::itemAlias('National'), ['prompt' => Yii::t("app", "Select")]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'sex')->dropDownList(\common\models\Customer::itemAlias('Sex')) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'sh_number')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'birthday')->widget(MaskedInput::className(), ['mask' => '9999/99/99']) ?>
            </div>
			<div class="col-md-2">
				<?= $form->field($model, 'issue_date')->widget(MaskedInput::className(), ['mask' => '9999/99/99']) ?>
			</div>
			<div class="col-md-2">
				<?= $form->field($model, 'issue_place')->textInput(['maxlength' => true]) ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'marital')->dropDownList(EmployeeBranchUser::itemAlias('marital'), ['prompt' => Yii::t('app','Select...')]) ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'child_count')->textInput(['type' => 'number']) ?>
			</div>
            <div class="col-md-2">
                <?= $form->field($model, 'count_insurance_addition')->textInput(['type' => 'number'])->hint('تعداد نفرات برای کسر بیمه تکمیلی') ?>
            </div>
			<div class="col-md-3">
				<?= $form->field($model, 'education')->dropDownList(EmployeeBranchUser::itemAlias('education'), ['prompt' => Yii::t('app', 'Select...')]) ?>
			</div>

            <div class="col-md-3">
                <?= $form->field($model, 'job_code')->dropDownList(SalaryInsurance::itemAlias('List'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'insurance_code')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'start_work')->widget(MaskedInput::className(), ['mask' => '9999/99/99']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'end_work')->widget(MaskedInput::className(), ['mask' => '9999/99/99']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'checkout')->checkbox() ?>
            </div>
			<div class="col-md-6">
				<?= $form->field($model, 'description_work')->textarea(['rows' => 1]) ?>
			</div>
            <div class="col-md-3">
                <?= $form->field($model, 'insurance_history_month_count')->hint('تعداد روز سابقه بیمه از شرکت های قبلی') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'work_history_day_count')->hint('تعداد روز کارکرد ثبت نشده در سیستم از سالهای قبل') ?>
            </div>
			<div class="col-md-6">
				<?= $form->field($model, 'work_address')->textarea(['rows' => 1]) ?>
			</div>

			<div class="col-md-12">
				<?= $form->field($model, 'employee_address')->textarea(['rows' => 1]) ?>
			</div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
