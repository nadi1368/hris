<?php


use hesabro\hris\models\SalaryPeriod;
use yii\bootstrap4\ButtonDropdown;
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
            ['pre-confirm', 'id' => $salaryPeriod->id],
            [
                'title' => Yii::t('app', 'Confirm'),
                'class' => "showModalButton btn btn-primary ml-1",
                'data-title' => Yii::t('app', 'Confirm'),
            ]) : Html::a(Yii::t('app', 'Confirm'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_CONFIRM): ?>
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
            ]) : Html::a(Yii::t('app', 'Return State'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
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
<?php if ($salaryPeriod->status == SalaryPeriod::STATUS_PAYMENT): ?>
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
            ]) : Html::a(Yii::t('app', 'Return State'),
            'javascript:void(0)',
            [
                'data-pjax' => '0',
                'class' => "btn btn-secondary alert-btn ml-1",
                //'data-alert-title' => 'post',
                'data-alert-text' => $salaryPeriod->error_msq,
            ]) ?>
<?php endif; ?>
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
<?= $salaryPeriod->canDelete() ?
    Html::a(Yii::t('app', 'Delete'),
        ['salary-period/delete', 'id' => $salaryPeriod->id],
        [
            'data-pjax' => '0',
            'class' => "btn btn-danger ml-1 ",
            'data-title' => Yii::t('app', 'Delete'),
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
<?php
$operationItems = [];
if($salaryPeriod->status !== SalaryPeriod::STATUS_WAIT_CONFIRM)
{
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-file-excel']) . ' ' . Yii::t('app', 'Excel'),
        'url' => ['export', 'id' => $salaryPeriod->id],
        'encode' => false,
        'linkOptions' => [
            'data-pjax' => '0'
        ],
    ];
}
if($salaryPeriod->status === SalaryPeriod::STATUS_PAYMENT)
{
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-file-excel']) . ' ' . 'اکسل بانک با ابرسا',
        'url' => 'javascript:void(0)',
        'encode' => false,
        'linkOptions' => [
            'title' => 'اکسل بانک با ابرسا',
            'data-size' => 'modal-lg',
            'data-title' => 'اکسل بانک با ابرسا',
            'data-toggle' => 'modal',
            'data-target' => '#modal-pjax',
            'data-url' => Url::to(['excel-bank-with-native', 'id' => $salaryPeriod->id]),
            'data-hide-modal' => 0,
            'data-reload-pjax-container-modal' => 'body-excel-bank-with-native',
        ],
    ];
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-file']) . ' ' . 'خروجی بیمه با ابرسا',
        'url' => 'javascript:void(0)',
        'encode' => false,
        'linkOptions' => [
            'title' => 'خروجی بیمه با ابرسا',
            'data-title' => 'خروجی بیمه با ابرسا',
            'data-size' => 'modal-xl',
            'data-toggle' => 'modal',
            'data-target' => '#modal-pjax',
            'data-url' => Url::to(['insurance-with-native', 'id' => $salaryPeriod->id]),
            'data-hide-modal' => 0,
            'data-reload-pjax-container-modal' => 'body-insurance-with-native',
        ],
    ];
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-file']) . ' ' . 'خروجی بیمه با ابرسا برای تغیر شعبه',
        'url' => 'javascript:void(0)',
        'encode' => false,
        'linkOptions' => [
            'data-title' => 'خروجی بیمه با ابرسا برای تغیر شعبه بیمه تمام کارمندان',
            'title' => 'خروجی بیمه با ابرسا برای تغیر شعبه بیمه تمام کارمندان',
            'data-size' => 'modal-xl',
            'data-toggle' => 'modal',
            'data-target' => '#modal-pjax',
            'data-url' => Url::to(['insurance-with-native-set-end-work', 'id' => $salaryPeriod->id]),
            'data-hide-modal' => 0,
            'data-reload-pjax-container-modal' => 'body-insurance-with-native',
        ],
    ];
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-print']) . ' ' . 'چاپ بیمه با ابرسا',
        'url' => 'javascript:void(0)',
        'encode' => false,
        'linkOptions' => [
            'title' => 'چاپ بیمه با ابرسا',
            'data-size' => 'modal-lg',
            'data-title' => 'چاپ بیمه با ابرسا',
            'data-toggle' => 'modal',
            'data-target' => '#modal-pjax',
            'data-url' => Url::to(['print-insurance-with-native', 'id' => $salaryPeriod->id]),
            'data-hide-modal' => 0,
            'data-reload-pjax-container-modal' => 'body-insurance-with-native',
        ],
    ];
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-file-excel']) . ' ' . 'فایل مزایای غیر نقدی',
        'url' => ['excel-bank-non-cash-with-native', 'id' => $salaryPeriod->id],
        'encode' => false,
        'linkOptions' => [
            'class' => 'showModalButton',
            'title' => 'فایل مزایای غیر نقدی',
            'data-title' => 'فایل مزایای غیر نقدی',
        ],
    ];
    $operationItems[] = [
        'label' => Html::tag('span', '', ['class' => 'fa fa-print']) . ' ' . Yii::t('app', 'Print'),
        'url' => ['print', 'id' => $salaryPeriod->id],
        'encode' => false,
        'linkOptions' => [
            'data-pjax' => 0
        ],
    ];
}
$operationItems[] = [
    'label' => Html::tag('span', '', ['class' => 'fa fa-file']) . ' ' . 'خروجی فایل مالیات',
    'url' => ['salary-period-items/tax-with-native', 'id' => $salaryPeriod->id],
    'encode' => false,
    'linkOptions' => [
        'title' => 'خروجی فایل مالیات',
        'class' => "showModalButton",
    ],
];
$operationItems[] = [
    'label' => Html::tag('span', '', ['class' => 'fa fa-history']) . ' ' . Yii::t('app', 'Log'),
    'url' => ['/mongo/log/view-ajax', 'modelId' => $salaryPeriod->id, 'modelClass' => get_class($salaryPeriod)],
    'encode' => false,
    'linkOptions' => [
        'class' => 'showModalButton',
        'data-size' => 'modal-xxl',
        'title' => Yii::t("app", "Log")
    ],
];
?>
<?= ButtonDropdown::widget([
    'label' => 'عملیات',
    'options' => ['class' => ''],
    'buttonOptions' => ['class' => 'btn btn-secondary dropdown-toggle ml-1', 'title' => Yii::t('app', 'Actions')],
    'encodeLabel' => false,
    'dropdown' => [
        'items' => $operationItems
    ],
])
?>