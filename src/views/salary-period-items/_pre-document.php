<?php
use hesabro\hris\models\SalaryPeriod;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
?>

<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_WAIT_CONFIRM): ?>
    <?php
    $totalSalary = $salaryPeriod->getSalaryPeriodItems()->sum('total_salary');
    $insuranceOwner = $salaryPeriod->getSalaryPeriodItems()->sum('insurance_owner');
    $totalDebtor = $totalSalary + $insuranceOwner;

    $insuranceTotal = $salaryPeriod->getSalaryPeriodItems()->sum('insurance+insurance_owner');
    $taxTotal = $salaryPeriod->getSalaryPeriodItems()->sum('tax');
    $taxPayment = $salaryPeriod->getSalaryPeriodItems()->sum('payment_salary');
    $totalCreditor = $insuranceTotal + $taxTotal + $taxPayment;
    ?>
    <div class="card">
        <div class="card-header">
            پیش نمایش سند
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>حقوق و دستمزد</th>
                    <th>بیمه سهم کارفرما</th>
                    <th>جمع بدهکار</th>
                    <th>تامین اجتماعی</th>
                    <th>امور مالیاتی</th>
                    <th>حقوق پرداختنی</th>
                    <th>جمع بستانکار</th>
                    <th>مانده</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= number_format((float)$totalSalary) ?></td>
                    <td><?= number_format((float)$insuranceOwner) ?></td>
                    <td><?= number_format((float)$totalDebtor); ?></td>
                    <td><?= number_format((float)$insuranceTotal); ?></td>
                    <td><?= number_format((float)$taxTotal) ?></td>
                    <td><?= number_format((float)$taxPayment) ?></td>
                    <td><?= number_format((float)$totalCreditor); ?></td>
                    <td>
                        <?= number_format($totalDebtor - $totalCreditor) ?>
                        <?= $totalDebtor - $totalCreditor != 0 ? Html::a('بررسی', ['salary-period-items/check-document', 'id' => $salaryPeriod->id], ['class' => 'btn btn-primary']) : ''; ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
