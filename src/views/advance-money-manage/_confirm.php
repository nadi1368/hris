<?php

use common\models\Account;
use common\models\AccountDefinite;
use common\models\Operation;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model backend\models\AdvanceMoney */
/* @var $operation common\models\Operation */

$get_account_url = Url::to(['/account-definite/find', 'level' => AccountDefinite::LEVEL_DEFINITE, 'is_account' => 1]);
?>

<div class="customer-adress-form">

    <?php $form = ActiveForm::begin([
        'id' => 'ajax-operation'
    ]); ?>
    <div class="card-body">
        <div class="row">
			<div class="col-md-4">
                <?= $form->field($operation, "m_id_debtor")->widget(Select2::class, [
                    'initValueText' => $operation->m_id_debtor ? $operation->mDebtor->title : 0, // set the initial display text
                    'options' => ['placeholder' => Yii::t("app", "Search"), 'dir' => 'rtl'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 2,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'خطا در جستجوی اطلاعات'; }"),
                            'inputTooShort' => new JsExpression("function () { return 'لطفا تایپ نمایید'; }"),
                            'loadingMore' => new JsExpression("function () { return 'بارگیری بیشتر'; }"),
                            'noResults' => new JsExpression("function () { return 'نتیجه ای یافت نشد.'; }"),
                            'searching' => new JsExpression("function () { return 'در حال جستجو...'; }"),
                            'maximumSelected' => new JsExpression("function () { return 'حداکثر انتخاب شده'; }"),
                        ],
                        'ajax' => [
                            'url' => $get_account_url,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text_show; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($operation, 'to')->widget(Select2::class, [
                    'data' => Account::itemAlias('Customer', null, $model->user->customer->id),
                    'options' => ['placeholder' => Yii::t("app", "Search")],
                    'pluginOptions' => [
                        //'allowClear' => true
                    ],
                ]); ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($operation, 'from')->widget(Select2::class, [
                    'data' => Account::itemAlias('BankOrCashOrPublic'),
                    'options' => ['placeholder' => Yii::t("app", "Search")],
                    'pluginOptions' => [
                        //'allowClear' => true
                    ],
                ]); ?>
            </div>
            <div class="col-md-3">
               <?= $form->field($operation, 'price')
                   ->widget(MaskedInput::class,
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
                       ]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($operation, 'date')->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                ]) ?>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-2">
                <?= $form->field($operation, 'serial') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($operation, 'wage')->widget(MaskedInput::class,
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
                       ]) ?>
            </div>

			<div class="col-md-2">
				<?= $form->field($operation, 'wage_type')->radioList(Operation::itemAlias('WageType')) ?>
			</div>

            <div class="col-md-12">
                <?= $form->field($operation, 'des')->textarea(['rows' => 1]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success ']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>