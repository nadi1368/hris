<?php

use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-branch-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'manager')->widget(Select2::class, array(
                    'data' => Module::getInstance()->user::getUserWithRoles(Module::getInstance()->employeeRole),
                    'options' => array(
                        'placeholder' => '',
                        'dir' => 'rtl',
                    ),
                )); ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'user_ids')->widget(Select2::class, array(
                    'data' => Module::getInstance()->user::getUserWithRoles(Module::getInstance()->employeeRole),
                    'options' => array(
                        'placeholder' => 'کارمندان این شعبه',
                        'dir' => 'rtl',
                        'multiple' => true
                    ),
                )); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, "definite_id_salary")->widget(Select2::class, [
                    'initValueText' => $model->getDefiniteSalaryTitle(), // set the initial display text
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
                            'url' => $model->getDefiniteUrl(),
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
                <?= $form->field($model, "account_id_salary")->widget(Select2::class, [
                    'initValueText' => $model->getAccountSalaryTitle(), // set the initial display text
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
                            'url' => $model->getAccountUrl(),
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

            <div class="col-md-6">
                <?= $form->field($model, "definite_id_insurance_owner")->widget(Select2::class, [
                    'initValueText' => $model->getDefiniteInsuranceTitle(), // set the initial display text
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
                            'url' => $model->getDefiniteUrl(),
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
                <?= $form->field($model, "account_id_insurance_owner")->widget(Select2::class, [
                    'initValueText' => $model->getAccountInsuranceTitle(), // set the initial display text
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
                            'url' => $model->getAccountUrl(),
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
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module','Create') : Module::t('module','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
