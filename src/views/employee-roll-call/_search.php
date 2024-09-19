<?php

use common\models\User;
use common\widgets\dateRangePicker\dateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeRollCallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-roll-call-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-3">
                <?= $form->field($model, 'user_id')->widget(Select2::className(), [
                    'data' => User::getUserWithRoles(['employee']),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'placeholder' => Yii::t('app', 'Search'),
                        'dir' => 'rtl',
                    ],
                ]); ?>
            </div>

            <div class="col-md-3 date-input">
                <?= $form->field($model, 'fromDate')->widget(MaskedInput::class, [
                                'mask' => '9999/99/99',
                            ]); ?>
            </div>

            <div class="col-md-3 date-input">
                <?= $form->field($model, 'toDate')->widget(MaskedInput::class, [
                                'mask' => '9999/99/99',
                            ]); ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'status') ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
