<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\Year;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryBase */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="salary-base-form">

    <?php $form = ActiveForm::begin(['id'=>'form-salary-base']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'year')->dropDownList(Year::itemAlias('ListWithYear')) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'group')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-12"></div>
            <div class="col-md-4">
                <?= $form->field($model, "cost_of_year")
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
                <?= $form->field($model, "cost_of_work")
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
                <?= $form->field($model, "cost_of_hours")
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

        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
