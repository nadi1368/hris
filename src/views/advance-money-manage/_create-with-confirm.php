<?php

use common\models\Account;
use common\widgets\WageFormWidget;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;

/**
 * @var $this yii\web\View
 * @var $model hesabro\hris\models\AdvanceMoneyForm
 * @var string $accountList
 * @var string $accountDefiniteFind
 */

?>

<?php $form = ActiveForm::begin([
    'id' => 'form-checkout-ipg'
]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, "definite_id_from")->widget(Select2::class, [
                    'initValueText' => $model->definiteFrom ? $model->definiteFrom->title : 0, // set the initial display text
                    'options' => ['placeholder' => Module::t('module', "Search"), 'dir' => 'rtl'],
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
                            'url' => $accountDefiniteFind,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text_show; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                ]); ?>

            </div>
            <div class="col-md-6">
                <?= $form->field($model, "account_id_from")->widget(Select2::class, [
                    'initValueText' => $model->accountFrom ? $model->accountFrom->fullName : 0, // set the initial display text
                    'options' => ['placeholder' => Module::t('module', "Search")],
                    'pluginOptions' => [
                        //'allowClear' => true,
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
                            'url' => $accountList,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(city) { return city.text_show; }'),
                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                        'dropdownParent' => '#modal-pjax'
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, "amount")
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
            <div class="col-md-2 date-input">
                <?= $form->field($model, 'date')->widget(MaskedInput::class, ['mask' => '9999/99/99']) ?>
                <?= $form->field($model, 'btn_type')->hiddenInput()->label(false) ?>
            </div>
            <div class="col-md-8">
                <?= $form->field($model, 'description')->textInput() ?>
            </div>
            <div class="col-md-12">
                <?= WageFormWidget::widget(['model' => $model, 'form' => $form]) ?>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' => 'btn btn-success', 'name' => 'TypeBtn', 'value' => 'save']) ?>
        <?= Html::submitButton(Module::t('module', 'Save and see documents'), ['class' => 'btn btn-info', 'name' => 'TypeBtn', 'value' => 'document']) ?>
    </div>
<?php ActiveForm::end(); ?>
