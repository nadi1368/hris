<?php

use hesabro\hris\models\SalaryPeriodItems;
use common\components\Helper;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $model hesabro\hris\models\SalaryPeriodItems */
/* @var $form yii\bootstrap4\ActiveForm */
$this->registerJsFile("@web/js/salary-calculate.js?v=1.1.25", ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = 'calculateSalary();';
$this->registerJs($js);
?>

<div class="salary-period-items-form">
    <?php if ($model->employee->end_work > 0): ?>
        <div class="alert alert-info">
            <p>این کارمند در تاریخ <?= $model->employee->end_work ?> ترک کار کرده است</p>
        </div>
    <?php endif; ?>
    <?php $form = ActiveForm::begin(['id' => 'form-salary-period-items']); ?>
    <div class="card-body p-0">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'hours_of_work')
                    ->textInput([
                        'onchange' => "return calculateSalary()",
                        'data-count-day' => $model->period->countDay,
                        'data-manager' => "{$model->employee->manager}"
                    ])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'hours_of_work')) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'treatment_day')
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'treatment_day'))
                    ->label(isset($model->detailAddition['treatment_day']) ? Helper::renderLabelHelp($model->getAttributeLabel('treatment_day'), implode("<br />", $model->detailAddition['treatment_day'])) : $model->getAttributeLabel('treatment_day'));
                ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'countOfDayLeaveNoSalary')->textInput(['disabled' => true])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'countOfDayLeaveNoSalary'))
                    ->label(isset($model->detailAddition['countOfDayLeaveNoSalary']) ? Helper::renderLabelHelp($model->getAttributeLabel('countOfDayLeaveNoSalary'), implode("<br />", $model->detailAddition['countOfDayLeaveNoSalary'])) : $model->getAttributeLabel('countOfDayLeaveNoSalary')) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, "basic_salary")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'onchange' => "return calculateSalary()"
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ])->hint(SalaryPeriodItems::itemAlias('HintLabel', 'basic_salary')) ?>
            </div>

            <div class="col-md-12"></div>

            <div class="col-md-3">
                <?= $form->field($model, "cost_of_food")
                    ->checkbox(['value' => $model->cost_of_food, 'onchange' => "return calculateSalary()", 'data-value' => ($model->year->COST_OF_FOOD / $model->period->countDay), 'data-full' => $model->year->COST_OF_FOOD, 'checked' => $model->cost_of_food > 0 ? true : false])
                    ->label('<span>' . number_format((float)$model->cost_of_food) . '</span>' . " ریال حق بن و خوارو بار") ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "cost_of_house")
                    ->checkbox(['value' => $model->cost_of_house, 'onchange' => "return calculateSalary()", 'data-value' => ($model->year->COST_OF_HOUSE / $model->period->countDay), 'data-full' => $model->year->COST_OF_HOUSE, 'checked' => $model->cost_of_house > 0 ? true : false])
                    ->label('<span>' . number_format((float)$model->cost_of_house) . '</span>' . " ریال حق مسکن") ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "cost_of_spouse")
                    ->checkbox(['value' => $model->cost_of_spouse, 'onchange' => "return calculateSalary()", 'data-value' => ((int)$model->year->COST_OF_SPOUSE / $model->period->countDay), 'data-full' => (int)$model->year->COST_OF_SPOUSE, 'checked' => $model->cost_of_spouse > 0 ? true : false])
                    ->label('<span>' . number_format($model->cost_of_spouse) . '</span>' . " ریال حق عائله مندی") ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "rate_of_year")
                    ->checkbox(['value' => $model->rate_of_year, 'onchange' => "return calculateSalary()", 'data-value' => ($model->getRateOfYearFromHistory() / $model->period->countDay), 'data-full' => $model->getRateOfYearFromHistory(), 'checked' => $model->rate_of_year > 0 ? true : false])
                    ->label('<span>' . number_format($model->getRateOfYearFromHistory()) . '</span>' . " ریال سنوات") ?>
                <?php if ($model->employee->manager): ?>
                    <p class="text-info">
                        <?= "عضو هیات مدیره" ?>
                    </p>
                <?php endif; ?>
                <p class="font-10 <?= $model->historyOfWork > 365 ? 'text-success' : 'text-info' ?>">
                    <?= $model->historyOfWork . " روز کارکرد قبل از این ماه " . " (" . $model->historyOfWorkConvertToYear . " سال)" ?>
                </p>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'count_of_children')
                    ->dropdownList(SalaryPeriodItems::itemAlias('Children'), [
                        'prompt' => Yii::t('app', 'Select...'),
                        'onchange' => "return calculateCostOfChildrenAndSalary();",
                        'options' => $model->getChildrenCost()
                    ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "cost_of_children")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true,
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
            <div class="col-md-3">
                <?= $form->field($model, "commission")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'onchange' => "return calculateSalary()"
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ])
                    ->label(isset($model->detailAddition['commission']) ? Helper::renderLabelHelp($model->getAttributeLabel('commission'), implode("<br />", $model->detailAddition['commission'])) : $model->getAttributeLabel('commission')) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, "cost_of_trust")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'onchange' => "return calculateSalary()"
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true]
                        ])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'cost_of_trust')) ?>
            </div>

            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model, 'hours_of_overtime')
                    ->textInput(['data-value' => $model->year->COST_HOURS_OVERTIME, 'onchange' => "return calculateSalary()"])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'hours_of_overtime'))
                    ->label(isset($model->detailAddition['hours_of_overtime']) ?
                        Helper::renderLabelHelp($model->getAttributeLabel('hours_of_overtime'), implode("<br />", $model->detailAddition['hours_of_overtime'])) :
                        $model->getAttributeLabel('hours_of_overtime')
                    ) ?>
                <?= $form->field($model, "hours_of_overtime_cost")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true]
                        ])
                    ->label(false) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'holiday_of_overtime')
                    ->textInput(['data-value' => $model->year->COST_HOLIDAY_OVERTIME, 'onchange' => "return calculateSalary()"])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'holiday_of_overtime'))
                    ->label(isset($model->detailAddition['holiday_of_overtime']) ?
                        Helper::renderLabelHelp($model->getAttributeLabel('holiday_of_overtime'), implode("<br />", $model->detailAddition['holiday_of_overtime'])) :
                        $model->getAttributeLabel('holiday_of_overtime')
                    );
                ?>
                <?= $form->field($model, "holiday_of_overtime_cost")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true]
                        ])
                    ->label(false) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'night_of_overtime')
                    ->textInput(['data-value' => $model->year->COST_NIGHT_OVERTIME, 'onchange' => "return calculateSalary()"])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'night_of_overtime'))
                    ->label(isset($model->detailAddition['night_of_overtime']) ?
                        Helper::renderLabelHelp($model->getAttributeLabel('night_of_overtime'), implode("<br />", $model->detailAddition['night_of_overtime'])) :
                        $model->getAttributeLabel('night_of_overtime')
                    ); ?>
                <?= $form->field($model, "night_of_overtime_cost")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true
                            ],
                            'clientOptions' => ['alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true
                            ]
                        ])
                    ->label(false) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'hoursOfLowTime')
                    ->textInput(['data-value' => $model->year->COST_HOURS_LOW_TIME, 'onchange' => "return calculateSalary()"])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'hoursOfLowTime'))
                    ->label(isset($model->detailAddition['hoursOfLowTime']) ?
                        Helper::renderLabelHelp($model->getAttributeLabel('hoursOfLowTime'), implode("<br />", $model->detailAddition['hoursOfLowTime'])) :
                        $model->getAttributeLabel('hoursOfLowTime')
                    ); ?>
                <?= $form->field($model, "hoursOfLowTimeCost")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true
                            ],
                            'clientOptions' => ['alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true
                            ]
                        ])
                    ->label(false) ?>
            </div>

            <div class="col-md-12"></div>

            <div class="col-md-3">
                <?= $form->field($model, "non_cash_commission")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'onchange' => "return calculateSalary()",
                                'disabled' => true,
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ])->label(isset($model->detailAddition['non_cash_commission']) ?
                        Helper::renderLabelHelp($model->getAttributeLabel('non_cash_commission'), implode("<br />", $model->detailAddition['non_cash_commission'])) :
                        $model->getAttributeLabel('non_cash_commission')
                    ); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "insurance")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true,
                                'data-value' => $model->employee->manager ? $model->year->COST_INSURANCE_MANAGER : $model->year->COST_INSURANCE,
                                'data-immunity_insurance' => $model->year->getIMMUNITYINSURANCE(),
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ]
                        ]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "insurance_owner")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true,
                                'data-value' => $model->employee->manager ? $model->year->COST_INSURANCE_OWNER_MANAGER : $model->year->COST_INSURANCE_OWNER
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ]
                        ]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "tax")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true,
                                'data-step-1-min' => $model->year->COST_TAX_STEP_1_MIN,
                                'data-step-2-min' => $model->year->COST_TAX_STEP_2_MIN,
                                'data-step-3-min' => $model->year->COST_TAX_STEP_3_MIN,
                                'data-step-4-min' => $model->year->COST_TAX_STEP_4_MIN,
                                'data-step-1-percent' => $model->year->COST_TAX_STEP_1_PERCENT,
                                'data-step-2-percent' => $model->year->COST_TAX_STEP_2_PERCENT,
                                'data-step-3-percent' => $model->year->COST_TAX_STEP_3_PERCENT,
                                'data-step-4-percent' => $model->year->COST_TAX_STEP_4_PERCENT,
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
            <div class="col-md-12"></div>
            <div class="col-md-3">
                <?= $form->field($model, "total_salary")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'readonly' => true
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true]
                        ])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'total_salary')) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, "advance_money")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'onchange' => "return calculateSalary()"
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true]
                        ]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, "insurance_addition")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true
                            ]
                        ])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'insurance_addition')) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "payment_salary")
                    ->widget(MaskedInput::class,
                        [
                            'options' =>
                                [
                                    'autocomplete' => 'off',
                                    'readonly' => true
                                ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true]
                        ])
                    ->hint(SalaryPeriodItems::itemAlias('HintLabel', 'payment_salary')) ?>
            </div>
            <div class="col-md-9 text-right">
                <div class="b-label">
                    <label for="inputEmail3" class="control-label col-form-label"><?= SalaryPeriodItems::itemAlias('HintLabel', 'final_payment') ?></label>
                </div>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "final_payment")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true,
                                'class'=>'form-control text-success'
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true
                            ]
                        ])->label(false) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'descriptionShowEmployee')->textarea(['rows' => 2]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',  'name' => 'TypeSubmit', 'value' => 'default']) ?>
        <?= !$model->isNewRecord ?  Html::submitButton( 'ذخیره و نمایش فرم کارمند بعدی', ['class' =>  'btn btn-info',  'name' => 'TypeSubmit', 'value' => 'next']) : '' ?>

        <button type="button" class="btn btn-primary pull-left" data-toggle="modal" data-target="#formulaModal">
            نمایش نحوه محاسبه حقوق
        </button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
