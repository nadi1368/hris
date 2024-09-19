<?php


/* @var $this yii\web\View */

use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\SalaryItemsAddition;
use common\models\BalanceDetailed;
use common\models\Settings;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use hesabro\hris\models\SalaryPeriodItems;

/* @var $model hesabro\hris\models\SalaryPeriod */
/* @var $varianceAdvanceMoney SalaryPeriodItems[] */
/* @var $varianceSalaryItemsAddition SalaryItemsAddition[] */
/* @var $varianceHoursOfLowTime SalaryPeriodItems[] */
/* @var $varianceComfortItems ComfortItems[] */

$showUpdateBtn = false;
$link = [];
$link['SalaryPeriodItemsSearch']['user_id'] = [];
?>
<div class="card">
    <?php if ($varianceAdvanceMoney): ?>
        <div class="card-body">
            <div class="alert alert-info">
                <p>کارمندان زیر مساعده دریافت شده ثبت نشده دارند</p>
            </div>
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>کارمند</th>
                    <th>مساعده ثبت شده</th>
                    <th>مساعده دریافت شده</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($varianceAdvanceMoney as $index => $item): ?>
                    <?php $showUpdateBtn = true; ?>
                    <?php $link['SalaryPeriodItemsSearch']['user_id'][] = $item->user_id; ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= $item->user->linkEmployee ?></td>
                        <td><?= number_format((float)$item->advance_money) ?></td>
                        <td><?= number_format((float)BalanceDetailed::getBalance(Settings::get('m_debtor_advance_money'), $item->user->customer->oneAccount->id, false)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if ($varianceSalaryItemsAddition): ?>
        <div class="card-body">
            <div class="alert alert-info">
                <p>کارمندان زیر اضافات کسورات و پورسانت های دریافت شده ثبت نشده دارند</p>
            </div>
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>کارمند</th>
                    <th>نوع</th>
                    <th>مقدار</th>
                    <th>تاریخ</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($varianceSalaryItemsAddition as $index => $itemAddition): ?>
                    <?php $showUpdateBtn = true; ?>
                    <?php $link['SalaryPeriodItemsSearch']['user_id'][] = $itemAddition->user_id; ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= $itemAddition->user->linkEmployee ?></td>
                        <td><?= SalaryItemsAddition::itemAlias('Kind', $itemAddition->kind) ?></td>
                        <td><?= $itemAddition->getValue() ?></td>
                        <td><?= $itemAddition->getDate() ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if ($varianceComfortItems): ?>
        <div class="card-body">
            <div class="alert alert-info">
                <p>کارمندان زیر امکانات رفاهی دریافت شده ثبت نشده دارند</p>
            </div>
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>کارمند</th>
                    <th>نوع</th>
                    <th>مقدار</th>
                    <th>تاریخ</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($varianceComfortItems as $index => $itemComfortItems): ?>
                    <?php $showUpdateBtn = true; ?>
                    <?php $link['SalaryPeriodItemsSearch']['user_id'][] = $itemComfortItems->user_id; ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= $itemComfortItems->user->linkEmployee ?></td>
                        <td><?= $itemComfortItems->comfort->title ?></td>
                        <td><?= number_format((float)$itemComfortItems->amount) ?></td>
                        <td><?= Yii::$app->jdate->date("Y/m/d  H:i", $itemComfortItems->created) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if ($varianceHoursOfLowTime): ?>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <tbody>
                <?php foreach ($varianceHoursOfLowTime as $index => $item): ?>
                    <?php if ($item->getHoursOfLowTimeCost() > ($item->commission+$item->cost_of_trust)): ?>
                        <?php $showUpdateBtn = true; ?>
                        <?php $link['SalaryPeriodItemsSearch']['user_id'][] = $item->user_id; ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= $item->user->linkEmployee ?></td>
                            <td>
                                <div class="alert alert-info">
                                    <p>میزان کسر کار از حداقل حقوق بیشتر است</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div class="card-footer">
        <?= $showUpdateBtn ? Html::a(
            'مشاهده لیست کارمندان و بروز رسانی',
            ArrayHelper::merge(['index', 'id' => $model->id], $link),
            [
                'title' => 'مشاهده لیست کارمندان و بروز رسانی',
                'class' => "btn btn-success",
            ]) : ''; ?>
        <?= $model->canConfirm() ?
            Html::a(Yii::t('app', 'Confirm'),
                ['confirm', 'id' => $model->id],
                [
                    'title' => Yii::t('app', 'Confirm'),
                    'class' => "btn btn-primary",
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure?'),
                ]) : '' ?>
    </div>
</div>