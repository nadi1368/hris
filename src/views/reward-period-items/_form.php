<?php

use backend\models\BalanceDaily;
use hesabro\hris\models\SalaryBase;
use hesabro\hris\models\SalaryPeriodItems;
use common\models\Settings;
use common\models\Year;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $model hesabro\hris\models\SalaryPeriodItems */
/* @var $form yii\bootstrap4\ActiveForm */
$this->registerJsFile("@web/js/reward-calculate.js?v=1.1.8", ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = 'calculateSalary();';
$this->registerJs($js);

$paymentReward = BalanceDaily::find()->byDefinite(Settings::get('reward_period_payment_m_id'))->byAccount($model->employee->account_id)->andWhere(['between', 'b_date', Year::getDefault('start'), Year::getDefault('end')])->one();
$totalPaymentInYear=$model->getTotalInYear()  - ($model->year->COST_TAX_STEP_1_MIN) - ((int)$model->getTotalInYear('insurance') * 2 / 7);
?>

<div class="salary-period-items-form">

    <?php $form = ActiveForm::begin(['id' => 'form-salary-period-items']); ?>
    <div class="card-body">
        <div class="row">
            <?php if ($paymentReward !== null): ?>
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <p>این کاربر در این سال مالی گردش عیدی پاداش دارد.</p>
                        <p><?= Html::a(Yii::t('app', 'Details'), ['/account/cycle', 'DocumentDetailsSearch[definite_id]' => Settings::get('reward_period_payment_m_id'), 'DocumentDetailsSearch[a_id]' => $model->employee->account_id, 'DocumentDetailsSearch[fromDate]' => Year::getDefault('start'), 'DocumentDetailsSearch[toDate]' => Year::getDefault('end')], ['class' => 'text-info']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <?= $form->field($model, 'hours_of_work')->textInput(['onchange' => "return calculateSalary()"])->hint(SalaryPeriodItems::itemAlias('HintLabel', 'hours_of_work')) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, "basic_salary")
                    ->widget(MaskedInput::className(),
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

            <?php if($model->employee->end_work): ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>تاریخ ترک کار</label>
                        <input type="text"  class="form-control" name="SalaryPeriodItems[tax]" disabled="" autocomplete="off" value="<?= $model->employee->end_work ?>" >

                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-12"></div>


            <div class="col-md-3">
                <?= $form->field($model, "tax")
                    ->widget(MaskedInput::className(),
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'disabled' => true,
                                'data-tax-reward' => $model->year->COST_TAX_REWARD,
                                'data-tax-percent' => $model->year->COST_TAX_REWARD_PERCENT,
                                'data-payment-salary-in-year' => $model->getTotalInYear(),
                                'data-payment-tax-in-year' => $model->getTotalInYear('tax'),
                                'data-year-count-day' => $model->year->countDay,
                                'data-total-payment-in-year' => $totalPaymentInYear,
                                'data-step-1-min' => $model->year->COST_TAX_REWARD_STEP_1_MIN,
                                'data-step-2-min' => $model->year->COST_TAX_REWARD_STEP_2_MIN,
                                'data-step-3-min' => $model->year->COST_TAX_REWARD_STEP_3_MIN,
                                'data-step-4-min' => $model->year->COST_TAX_REWARD_STEP_4_MIN,
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
                    ->widget(MaskedInput::className(),
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
                        ])->hint(SalaryPeriodItems::itemAlias('HintLabel', 'total_salary')) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, "advance_money")
                    ->widget(MaskedInput::className(),
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'onchange' => "return calculateSalary()",
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
                <?= $form->field($model, "payment_salary")
                    ->widget(MaskedInput::className(),
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
                        ])->hint(SalaryPeriodItems::itemAlias('HintLabel', 'payment_salary')) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, "final_payment")
                    ->widget(MaskedInput::className(),
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
                        ])->hint(SalaryPeriodItems::itemAlias('HintLabel', 'final_payment')) ?>
            </div>

        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
