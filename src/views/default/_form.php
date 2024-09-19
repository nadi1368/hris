<?php

use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-branch-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'manager')->widget(Select2::class, array(
                    'data' => User::getUserWithRoles(['employee']),
                    'options' => array(
                        'placeholder' => '',
                        'dir' => 'rtl',
                    ),
                )); ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'user_ids')->widget(Select2::class, array(
                    'data' => User::getUserWithRoles(['employee']),
                    'options' => array(
                        'placeholder' => 'کارمندان این شعبه',
                        'dir' => 'rtl',
                        'multiple' => true
                    ),
                )); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
