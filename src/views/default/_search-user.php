<?php

use hesabro\hris\models\EmployeeBranch;
use hesabro\hris\models\EmployeeBranchUser;
use common\components\Helper;
use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-branch-search">

    <?php $form = ActiveForm::begin([
        'action' => ['users'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-3">
                <?= $form->field($model, 'branch_id')->dropDownList(EmployeeBranch::itemAlias('List'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, "user_id")->widget(Select2::class, [
                    'data' => User::getUserWithRoles(['employee']),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'dir' => 'rtl',
                        'placeholder' => Yii::t('app', 'Select...'),
                    ],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'show_on_salary_list')->dropDownList(Helper::itemAlias('YesOrNo'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'set_iban')->dropDownList(Helper::itemAlias('YesOrNo'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'status')->dropDownList(EmployeeBranchUser::itemAlias('Status'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>
            <div class="col-md-2 date-input">
                <?= $form->field($model, 'end_work')->widget(MaskedInput::className(), [
                    'mask' => '9999/99/99',
                ]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'roll_call_id')->dropDownList(Helper::itemAlias('YesOrNo'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Yii::t('app','Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app','Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
