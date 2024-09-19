<?php

use hesabro\hris\models\SalaryPeriod;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
?>
<?= $salaryPeriod->status !== SalaryPeriod::STATUS_WAIT_CONFIRM ? Html::a(Yii::t('app', 'Document'), $salaryPeriod->getDocumentLink(), [
    'class' => 'btn btn-success',
]) : '' ?>
<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_WAIT_CONFIRM): ?>
    <?= $salaryPeriod->canConfirm() ?
        Html::a(Yii::t('app', 'Confirm'),
            'javascript:void(0)',
            [
                'title' => Yii::t('app', 'Confirm'),
                'aria-label' => Yii::t('app', 'Confirm'),
                'data-reload-pjax-container' => 'p-jax-salary-period-items',
                'data-pjax' => '0',
                'data-url' => Url::to(['confirm', 'id' => $salaryPeriod->id]),
                'class' => "p-jax-btn btn btn-primary ml-1 ",
                'data-title' => Yii::t('app', 'Confirm'),
                'data-method' => 'post',
                'data-confirm' => Yii::t('app', 'Are you sure?'),
            ]) : Html::a(Yii::t('app', 'Confirm'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
<?= $salaryPeriod->canReturnConfirm() ?
    Html::a(Yii::t('app', 'Return State'),
        'javascript:void(0)',
        [
            'title' => Yii::t('app', 'Return State'),
            'aria-label' => Yii::t('app', 'Return State'),
            'data-reload-pjax-container' => 'p-jax-salary-period-items',
            'data-pjax' => '0',
            'data-url' => Url::to(['return-confirm', 'id' => $salaryPeriod->id]),
            'class' => "p-jax-btn btn btn-danger ml-1 ",
            'data-title' => Yii::t('app', 'Return State'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('app', 'Are you sure?'),
        ]) : '' ?>
<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_CONFIRM): ?>
<?= $salaryPeriod->canPayment() ?
    Html::a(Yii::t('app', 'Payment'),
                'javascript:void(0)', [
                    'title' => Yii::t('app', 'Payment'),
                    'id' => 'create-payment-period-salary',
                    'class' => 'btn btn-primary ml-1',
                    'data-title' => Yii::t('app', 'Payment'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['payment', 'id' => $salaryPeriod->id]),
                    'data-reload-pjax-container-on-show' => 0,
                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                    'data-handleFormSubmit' => 1,
                    'disabled' => true
        ]) : Html::a(Yii::t('app', 'Payment'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
<?= $salaryPeriod->canReturnPayment() ?
    Html::a(Yii::t('app', 'Return State'),
        'javascript:void(0)',
        [
            'title' => Yii::t('app', 'Return State'),
            'aria-label' => Yii::t('app', 'Return State'),
            'data-reload-pjax-container' => 'p-jax-salary-period-items',
            'data-pjax' => '0',
            'data-url' => Url::to(['return-payment', 'id' => $salaryPeriod->id]),
            'class' => "p-jax-btn btn btn-danger ml-1 ",
            'data-title' => Yii::t('app', 'Return State'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('app', 'Are you sure?'),
        ]) : '' ?>

<?= $salaryPeriod->canDeleteItems() ?
    Html::a(Yii::t('app', 'Delete All'),
        'javascript:void(0)',
        [
            'title' => Yii::t('app', 'Delete All'),
            'aria-label' => Yii::t('app', 'Delete All'),
            'data-reload-pjax-container' => 'p-jax-salary-period-items',
            'data-pjax' => '0',
            'data-url' => Url::to(['delete-all', 'id' => $salaryPeriod->id]),
            'class' => "p-jax-btn btn btn-danger ml-1 ",
            'data-title' => Yii::t('app', 'Delete All'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('app', 'Are you sure?'),
        ]) : '' ?>
<?= $salaryPeriod->canCopyPreviousPeriod() ?
    Html::a(Yii::t('app', 'Copy From Previous Period'),
        ['copy-from-previous-period', 'id' => $salaryPeriod->id],
        [
            'title' => Yii::t('app', 'Copy From Previous Period'),
            'data-pjax' => '0',
            'class' => "btn btn-info ml-1",
            'data-method' => 'post',
            'data-confirm' => Yii::t('app', 'Are you sure?'),
        ]) : '' ?>
<?= $salaryPeriod->status !== SalaryPeriod::STATUS_WAIT_CONFIRM ? Html::a(Yii::t('app', 'Excel'), ['export', 'id' => $salaryPeriod->id], ['class' => 'btn btn-info', 'data-pjax' => '0']) : '' ?>
<?= $salaryPeriod->status === SalaryPeriod::STATUS_PAYMENT ? Html::a('اکسل بانک با ابرسا',
    'javascript:void(0)', [
        'title' => 'اکسل بانک با ابرسا',
        'id' => 'create-payment-period',
        'class' => 'btn btn-info ml-1 ',
        'data-size' => 'modal-lg',
        'data-title' => 'اکسل بانک با ابرسا',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['excel-bank-with-native', 'id' => $salaryPeriod->id]),
        'data-hide-modal' => 0,
        'data-reload-pjax-container-modal' => 'body-excel-bank-with-native',
        'data-handleFormSubmit' => 1,
        'disabled' => true
    ]) : ''; ?>
<?= $salaryPeriod->status === SalaryPeriod::STATUS_PAYMENT ? Html::a('خروجی بیمه با ابرسا',
    'javascript:void(0)', [
        'title' => 'خروجی بیمه با ابرسا',
        'id' => 'create-insurance-period',
        'class' => 'btn btn-info ml-1 ',
        'data-size' => 'modal-lg',
        'data-title' => 'خروجی بیمه با ابرسا',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['insurance-with-native', 'id' => $salaryPeriod->id]),
        'data-hide-modal' => 0,
        'data-reload-pjax-container-modal' => 'body-insurance-with-native',
        'data-handleFormSubmit' => 1,
        'disabled' => true
    ]) : ''; ?>
<?= $salaryPeriod->status === SalaryPeriod::STATUS_PAYMENT ? Html::a('چاپ بیمه با ابرسا',
    'javascript:void(0)', [
        'title' => 'چاپ بیمه با ابرسا',
        'id' => 'print-insurance-period',
        'class' => 'btn btn-info ml-1 ',
        'data-size' => 'modal-lg',
        'data-title' => 'چاپ بیمه با ابرسا',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['print-insurance-with-native', 'id' => $salaryPeriod->id]),
        'data-hide-modal' => 0,
        'data-reload-pjax-container-modal' => 'body-insurance-with-native',
        'data-handleFormSubmit' => 1,
        'disabled' => true
    ]) : ''; ?>
<?= $salaryPeriod->status == SalaryPeriod::STATUS_PAYMENT ? Html::a(Yii::t('app', 'Print'), ['print', 'id' => $salaryPeriod->id],
    [
        'class' => 'btn btn-success ml-1 ',
        'data-pjax' => 0
    ]) : ''
?>
<?= Html::a(Yii::t('app', 'Logs'),
    ['/mongo/log/view-ajax', 'modelId' => $salaryPeriod->id, 'modelClass' => SalaryPeriod::class],
    [
        'class' => 'btn btn-secondary showModalButton ml-1 ',
        'title' => Yii::t('app', 'Logs'),
        'data-size' => 'modal-xl'
    ]
);
?>

