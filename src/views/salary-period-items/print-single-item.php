<?php

use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodItems;
use common\models\Settings;
use hesabro\hris\Module;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this View */
/* @var $salaryPeriod SalaryPeriod */
/* @var $model SalaryPeriodItems */
$this->title = '  فیش حقوقی دوره ' . $model->period->getTitleWithYear();
?>
<div class="row d-flex justify-content-center align-items-center" dir="rtl">
    <div class="col-12">
        <div class="row d-flex justify-content-center align-items-center">
            <?php if (($logo = Module::getInstance()->settings::get('company_logo_for_contracts'))): ?>
                <h1 class="text-center" style="margin-bottom: 0;">
                    <?= Html::img($logo, ['class' => 'mt-2 mb-2', 'style' => ['width' => '80.11mm']]); ?>
                </h1>
            <?php endif; ?>
        </div>
        <br>
        <div class='row d-flex justify-content-center align-items-center'>
            <?= $model->period->workshop->title . ' ---- فیش حقوقی دوره ' . $model->period->getTitleWithYear() ?>
        </div>
        <br>
    </div>
    <div class="col-12 text-center">
        <table class='table table-bordered' style="table-layout: fixed">
            <tbody>
            <tr>
                <td colspan='8'> مشخصات فردی</td>
            </tr>
            <tr>
                <td colspan="1">شماره کارمندی</td>
                <td colspan='1'><?= $model->employee->nationalCode ?></td>
                <td colspan='1'>تاریخ صدور</td>
                <td colspan='1'><?= Yii::$app->jdf->jdate('Y/m/d') ?></td>
                <td colspan='1'>شماره شبا</td>
                <td colspan='3'><?= $model->employee->shaba ?></td>
            </tr>
            <tr>
                <td colspan='1'>نام</td>
                <td colspan='1'><?= $model->user->first_name ?></td>
                <td colspan='1'>نام خانوادگی</td>
                <td colspan='1'><?= $model->user->last_name ?></td>
                <td colspan='1'>کد شغلی</td>
                <td colspan='1'><?= $model->employee->salaryInsurance ? (($model->employee->salaryInsurance->code ? $model->employee->salaryInsurance->code . '-' : '____') . $model->employee->salaryInsurance->group) : '' ?></td>
                <td colspan='1'>شماره بیمه</td>
                <td colspan='1'><?= $model->employee->insurance_code ?></td>
            </tr>
            <tr>
                <td colspan='4' class="text-center"><b>دریافتی</b></td>
                <td colspan='4' class="text-center"><b>کسورات</b></td>
            </tr>
            <tr>
                <td colspan='2'>دستمزد</td>
                <td colspan='2'><?= number_format((int)$model->basic_salary) ?></td>
                <td colspan='2'>بیمه</td>
                <td colspan='2'><?= number_format((int)$model->insurance) ?></td>
            </tr>
            <tr>
                <td colspan='2'>کارکرد</td>
                <td colspan='2'><?= $model->hours_of_work ? number_format((int)$model->hours_of_work) . ' روز ' : '' ?></td>
                <td colspan='2'>مالیات</td>
                <td colspan='2'><?= number_format((int)$model->tax) ?></td>
            </tr>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('cost_of_house') ?></td>
                <td colspan='2'><?= number_format((int)$model->cost_of_house) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('cost_of_food') ?></td>
                <td colspan='2'><?= number_format((int)$model->cost_of_food) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php if ($model->cost_of_trust>0): ?>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('cost_of_trust') ?></td>
                <td colspan='2'><?= number_format((int)$model->cost_of_trust) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php endif; ?>
            <?php if($model->cost_of_spouse>0): ?>
                <tr>
                    <td colspan='2'><?= $model->getAttributeLabel('cost_of_spouse') ?></td>
                    <td colspan='2'><?= number_format($model->cost_of_spouse) ?></td>
                    <td colspan='2'></td>
                    <td colspan='2'></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('cost_of_children') ?></td>
                <td colspan='2'><?= number_format((int)$model->cost_of_children) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('rate_of_year') ?></td>
                <td colspan='2'><?= number_format((int)$model->rate_of_year) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php if ($model->cost_point>0): ?>
            <tr>
                <td colspan='2'>امتیازات</td>
                <td colspan='2'><?= $model->cost_point ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php endif; ?>
            <?php if ($model->commission>0): ?>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('commission') ?></td>
                <td colspan='2'><?= number_format((int)$model->commission); ?></td>
                <td colspan='4'></td>
            </tr>
            <?php endif; ?>
            <?php if ($model->non_cash_commission>0): ?>
                <tr>
                    <td colspan='2'><?= $model->getAttributeLabel('non_cash_commission') ?></td>
                    <td colspan='2'><?= number_format($model->non_cash_commission); ?></td>
                    <td colspan='4'>
                        <?php
                        if (isset($model->detailAddition['non_cash_commission']) && is_array($model->detailAddition['non_cash_commission'])) {
                            foreach ($model->detailAddition['non_cash_commission'] as $item) {
                                echo Html::tag('p', $item);
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($model->hours_of_overtime>0): ?>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('hours_of_overtime') ?></td>
                <td colspan='2'><?= number_format((int)$model->getHoursOfOvertimeCost()).' ('.$model->hours_of_overtime.' ساعت '.')'; ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php endif; ?>
            <?php if ($model->holiday_of_overtime>0): ?>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('holiday_of_overtime') ?></td>
                <td colspan='2'><?= number_format((int)$model->getHolidayOfOvertimeCost()).' ('.$model->holiday_of_overtime.' ساعت '.')'; ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php endif; ?>
            <?php if ($model->night_of_overtime>0): ?>
            <tr>
                <td colspan='2'><?= $model->getAttributeLabel('night_of_overtime') ?></td>
                <td colspan='2'><?= number_format((int)$model->getNightOfOvertimeCost()).' ('.$model->night_of_overtime.' ساعت '.')'; ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <?php endif; ?>
            <?php if ($model->hoursOfLowTime>0): ?>
            <tr>
                <td colspan='2'></td>
                <td colspan='2'></td>
                <td colspan='2'><?= $model->getAttributeLabel('hoursOfLowTime') ?></td>
                <td colspan='2'><?= number_format((int)$model->getHoursOfLowTimeCost()).' ('.$model->hoursOfLowTime.' ساعت '.')'; ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan='2'>جمع دریافتی</td>
                <td colspan='2'><?= number_format((int)$model->total_salary) ?></td>
                <td colspan='2'>مساعده</td>
                <td colspan='2'><?= number_format((int)$model->advance_money); ?></td>
            </tr>
            <tr>
                <td colspan='2'>مبلغ خالص</td>
                <td colspan='2'><?= number_format((int)$model->payment_salary) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <tr>
                <td colspan='2'>مبلغ پرداختی</td>
                <td colspan='2'><?= number_format((int)$model->finalPayment) ?></td>
                <td colspan='2'></td>
                <td colspan='2'></td>
            </tr>
            <tr>
                <td colspan="8">
                    <p class="ltr text-right"><?= Html::encode($model->descriptionShowEmployee) ?></p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
