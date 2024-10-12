<?php

use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;

/**
 * @var object[] $wageTypes
 * @var yii\web\View $this
 * @var AdvanceMoney $model
 * @var object $operation
 * @var string $getAccountUrl
 * @var array $toList
 * @var array $fromList
 */
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
                            'url' => $getAccountUrl,
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
                    'data' => $toList,
                    'options' => ['placeholder' => Module::t('module', "Search")],
                    'pluginOptions' => [
                        //'allowClear' => true
                    ],
                ]); ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($operation, 'from')->widget(Select2::class, [
                    'data' => $fromList,
                    'options' => ['placeholder' => Module::t('module', "Search")],
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
				<?= $form->field($operation, 'wage_type')->radioList($wageTypes) ?>
			</div>

            <div class="col-md-12">
                <?= $form->field($operation, 'des')->textarea(['rows' => 1]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' => 'btn btn-success ']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>