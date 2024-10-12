<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\Year */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="year-form">

    <?php $form = ActiveForm::begin(['id' => 'form-salary-year']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($model, "COST_OF_FOOD")
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
                        ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "COST_OF_HOUSE")
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
                        ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "COST_OF_SPOUSE")
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
                        ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "COST_OF_CHILDREN")
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
                        ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "COST_OF_YEAR")
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
                        ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "MIN_BASIC_SALARY")
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
                        ]); ?>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_HOURS_OVERTIME'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_HOLIDAY_OVERTIME'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_NIGHT_OVERTIME'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_HOURS_LOW_TIME'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'CALCULATE_EMPLOYEE_DAY')->checkbox(); ?>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_INSURANCE'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_INSURANCE_OWNER'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_INSURANCE_MANAGER'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model,'COST_INSURANCE_OWNER_MANAGER'); ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model,'IMMUNITY_INSURANCE'); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "COST_INSURANCE_ADDITION")
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
                        ]); ?>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_STEP_1_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_STEP_2_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_STEP_3_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_STEP_4_MIN")
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
                        ]); ?>
            </div>

            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_REWARD_STEP_1_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_REWARD_STEP_2_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_REWARD_STEP_3_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_REWARD_STEP_4_MIN")
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
                        ]); ?>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model,'COST_TAX_STEP_1_PERCENT'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model,'COST_TAX_STEP_2_PERCENT'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model,'COST_TAX_STEP_3_PERCENT'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model,'COST_TAX_STEP_4_PERCENT'); ?>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_TAX_REWARD")
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
                        ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model,'COST_TAX_REWARD_PERCENT'); ?>
            </div>

            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model, "COST_USER_POINTS")
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
                        ]); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-flat submit btn-success' : 'btn btn-primary btn-flat']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
