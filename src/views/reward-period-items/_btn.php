<?php

use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
?>
<?= $salaryPeriod->status !== SalaryPeriod::STATUS_WAIT_CONFIRM ? Html::a(Module::t('module', 'Document'), $salaryPeriod->getDocumentLink(), [
    'class' => 'btn btn-success',
]) : '' ?>
<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_WAIT_CONFIRM): ?>
    <?= $salaryPeriod->canConfirm() ?
        Html::a(Module::t('module', 'Confirm'),
            'javascript:void(0)',
            [
                'title' => Module::t('module', 'Confirm'),
                'aria-label' => Module::t('module', 'Confirm'),
                'data-reload-pjax-container' => 'p-jax-salary-period-items',
                'data-pjax' => '0',
                'data-url' => Url::to(['confirm', 'id' => $salaryPeriod->id]),
                'class' => "p-jax-btn btn btn-primary ml-1 ",
                'data-title' => Module::t('module', 'Confirm'),
                'data-method' => 'post',
                'data-confirm' => Module::t('module', 'Are you sure?'),
            ]) : Html::a(Module::t('module', 'Confirm'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
<?= $salaryPeriod->canReturnConfirm() ?
    Html::a(Module::t('module', 'Return State'),
        'javascript:void(0)',
        [
            'title' => Module::t('module', 'Return State'),
            'aria-label' => Module::t('module', 'Return State'),
            'data-reload-pjax-container' => 'p-jax-salary-period-items',
            'data-pjax' => '0',
            'data-url' => Url::to(['return-confirm', 'id' => $salaryPeriod->id]),
            'class' => "p-jax-btn btn btn-danger ml-1 ",
            'data-title' => Module::t('module', 'Return State'),
            'data-method' => 'post',
            'data-confirm' => Module::t('module', 'Are you sure?'),
        ]) : '' ?>
<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_CONFIRM): ?>
<?= $salaryPeriod->canPayment() ?
    Html::a(Module::t('module', 'Payment'),
                'javascript:void(0)', [
                    'title' => Module::t('module', 'Payment'),
                    'id' => 'create-payment-period-salary',
                    'class' => 'btn btn-primary ml-1',
                    'data-title' => Module::t('module', 'Payment'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['payment', 'id' => $salaryPeriod->id]),
                    'data-reload-pjax-container-on-show' => 0,
                    'data-reload-pjax-container' => 'p-jax-salary-period-items',
                    'data-handleFormSubmit' => 1,
                    'disabled' => true
        ]) : Html::a(Module::t('module', 'Payment'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
<?= $salaryPeriod->canReturnPayment() ?
    Html::a(Module::t('module', 'Return State'),
        'javascript:void(0)',
        [
            'title' => Module::t('module', 'Return State'),
            'aria-label' => Module::t('module', 'Return State'),
            'data-reload-pjax-container' => 'p-jax-salary-period-items',
            'data-pjax' => '0',
            'data-url' => Url::to(['return-payment', 'id' => $salaryPeriod->id]),
            'class' => "p-jax-btn btn btn-danger ml-1 ",
            'data-title' => Module::t('module', 'Return State'),
            'data-method' => 'post',
            'data-confirm' => Module::t('module', 'Are you sure?'),
        ]) : '' ?>

<?= $salaryPeriod->canDeleteItems() ?
    Html::a(Module::t('module', 'Delete All'),
        'javascript:void(0)',
        [
            'title' => Module::t('module', 'Delete All'),
            'aria-label' => Module::t('module', 'Delete All'),
            'data-reload-pjax-container' => 'p-jax-salary-period-items',
            'data-pjax' => '0',
            'data-url' => Url::to(['delete-all', 'id' => $salaryPeriod->id]),
            'class' => "p-jax-btn btn btn-danger ml-1 ",
            'data-title' => Module::t('module', 'Delete All'),
            'data-method' => 'post',
            'data-confirm' => Module::t('module', 'Are you sure?'),
        ]) : '' ?>
<?= $salaryPeriod->canCopyPreviousPeriod() ?
    Html::a(Module::t('module', 'Copy From Previous Period'),
        ['copy-from-previous-period', 'id' => $salaryPeriod->id],
        [
            'title' => Module::t('module', 'Copy From Previous Period'),
            'data-pjax' => '0',
            'class' => "btn btn-info ml-1",
            'data-method' => 'post',
            'data-confirm' => Module::t('module', 'Are you sure?'),
        ]) : '' ?>
<?= $salaryPeriod->status !== SalaryPeriod::STATUS_WAIT_CONFIRM ? Html::a(Module::t('module', 'Excel'), ['export', 'id' => $salaryPeriod->id], ['class' => 'btn btn-info', 'data-pjax' => '0']) : '' ?>
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
<?= $salaryPeriod->status == SalaryPeriod::STATUS_PAYMENT ? Html::a(Module::t('module', 'Print'), ['print', 'id' => $salaryPeriod->id],
    [
        'class' => 'btn btn-success ml-1 ',
        'data-pjax' => 0
    ]) : ''
?>
<?= Html::a(Module::t('module', 'Logs'),
    ['/mongo/log/view-ajax', 'modelId' => $salaryPeriod->id, 'modelClass' => SalaryPeriod::OLD_CLASS_NAME],
    [
        'class' => 'btn btn-secondary showModalButton ml-1 ',
        'title' => Module::t('module', 'Logs'),
        'data-size' => 'modal-xl'
    ]
);
?>

