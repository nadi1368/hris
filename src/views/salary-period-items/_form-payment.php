<?php


use common\models\BalanceDetailed;
use common\models\Settings;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */
/* @var $item hesabro\hris\models\SalaryPeriodItems */
/* @var $variancePaymentItems hesabro\hris\models\SalaryPeriodItems[] */

$variancePayment = false;
$variancePaymentItems = [];
$variancePaymentAmount = [];
$showUpdateBtn = false;
$link=[];
$link['SalaryPeriodItemsSearch']['user_id'] = [];
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-payment',
    'method' => 'post',
]); ?>
<div class="card">
    <div class="card-body">
        <?php foreach ($model->getSalaryPeriodItems()->all() as $index => $item): ?>
            <?php if (($amount = (BalanceDetailed::getBalance(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id) * (-1))) !== $item->finalPayment): ?>
                <?php
                $showUpdateBtn = true;
                $link['SalaryPeriodItemsSearch']['user_id'][] = $item->user_id;
                $variancePayment = true;
                $variancePaymentItems[$index] = $item;
                $variancePaymentAmount[$index] = $amount;
                ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($variancePayment): ?>
        <div class="alert alert-info">
            <p>لیست کارمندان داراری مغایرت سند پرداختی با مبلغ پرداختی محاسبه شده: </p>
        </div>
        <table class="table table-bordered text-center">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>کارمند</th>
                    <th>سند پرداختی</th>
                    <th>مبلغ پرداختی</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($variancePaymentItems as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= $item->user->linkEmployee ?></td>
                        <td><?= number_format((float)$variancePaymentAmount[$index]) ?></td>
                        <td><?= number_format((float)$item->finalPayment) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6 date-input">
                    <?= $form->field($model, 'payment_date')->widget(MaskedInput::class, [
                        'mask' => '9999/99/99',
                    ]) ?>
                </div>
            </div>
    </div>
    <div class="card-footer">
        <?= $showUpdateBtn ? Html::a(
            'مشاهده لیست کارمندان و بروز رسانی',
            ArrayHelper::merge(['index', 'id' => $model->id], $link),
            [
                'title' => 'مشاهده لیست کارمندان و بروز رسانی',
                'class' => "btn btn-success",
            ]) : ''; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
        <?= Html::submitButton(Module::t('module', 'Payment'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
