<?php



/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $items hesabro\hris\models\SalaryPeriod[] */
?>
<div class="container-fluid" style="margin-bottom: 5%">
    <table class="table table-bordered rtl">
        <thead>
        <tr>
            <th width="30px">ردیف</th>
            <th>نام</th>
            <th>نام خانوادکی</th>
            <th>مالیات</th>
            <th>حقوق خالص</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $index => $item): ?>
            <?php $employee = $item->employee; ?>
            <tr class="text-center">
                <td><?= $index + 1 ?></td>
                <td><?= $employee->first_name ?></td>
                <td><?= $employee->last_name ?></td>
                <td><?= number_format((float)$item->tax) ?></td>
                <td><?= number_format((float)$item->payment_salary) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

