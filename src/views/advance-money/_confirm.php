<?php

use common\models\OrderPayBalance;
use common\models\Tafzil;
use common\models\Hesab;
use hesabro\helpers\widgets\WageFormWidget;
use hesabro\hris\models\AdvanceMoney;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\helpers\Url;

use common\models\Document;

/* @var $this yii\web\View */
/* @var $model AdvanceMoney */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin([
    'id' => 'ajax-form-confirm-advance-money'
]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, "m_debtor_id")->widget(Select2::classname(), [
                    'initValueText' => $model->m_debtor_id ? $model->mDebtor->halfName : 0, // set the initial display text
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
                            'url' => $model->getDefiniteUrl(),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(city) { return city.text_show; }'),
                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                    ],
                ]);
                ?>

            </div>
            <div class="col-md-4">
                <?= $form->field($model, 't_creditor_id')->widget(Select2::class, [
                    'data' => $model->getTCreditorItems(),
                    'options' => ['placeholder' => Yii::t("app", "Search")],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'receipt_number')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'receipt_date')->widget(MaskedInput::className(), ['mask' => '9999/99/99']) ?>
                <?= $form->field($model, 'btn_type')->hiddenInput()->label(false) ?>
            </div>

            <div class="col-md-12">
                <?= WageFormWidget::widget(['model' => $model, 'form' => $form]) ?>
            </div>

        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'value' => 'save']) ?>
        <?= Html::submitButton(Yii::t('app', 'Save and see documents'), ['class' => 'btn btn-info', 'value' => 'document']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$script = <<< JS

$("button.btn").click(function (evt) {
   
    $('#advancemoney-btn_type').val($(this).val());
});


JS;
$this->registerJs($script);

?>