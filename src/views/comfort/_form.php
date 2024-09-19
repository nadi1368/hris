<?php

use common\components\Jdate;
use common\models\Tags;
use common\models\User;
use common\widgets\TagsWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\hris\models\Comfort;
use common\models\Faq;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\Comfort */
/* @var $form yii\widgets\ActiveForm */

$css = <<<CSS
.select2-results__option {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    text-overflow: ellipsis;
}
CSS;

$this->registerCss($css);
$initValueTextTagsInclude = '';
if ($model->jobs && ($tags = Tags::find()->andWhere(['IN', 'id', $model->jobs])->all())) {
    $initValueTextTagsInclude = ArrayHelper::map($tags, "id", "title");
} else {
    $model->jobs = [];
}
$initValueTextTagsExclude = '';
if ($model->excluded_jobs && ($tags = Tags::find()->andWhere(['IN', 'id', $model->excluded_jobs])->all())) {
    $initValueTextTagsExclude = ArrayHelper::map($tags, "id", "title");
} else {
    $model->excluded_jobs = [];
}
?>

<div class="comfort-form">

    <?php $form = ActiveForm::begin(['id' => 'form-comfort']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-12 col-md-6">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'type')->dropDownList(Comfort::itemAlias('TypeCat'), ['prompt' => Yii::t('app', 'Select')]) ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'type_limit')->dropDownList(Comfort::itemAlias('TypeLimit'), ['prompt' => Yii::t('app', 'Select')]) ?>
            </div>

            <div class="col-12 col-md-3 date-input">
                <?= $form->field($model, 'expire_time')->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                    'options' => [
                        'autocomplete' => 'off',
                        'value' => $model->expire_time > 0 ? Yii::$app->jdate->date('Y/m/d', $model->expire_time) : ''
                    ]
                ]) ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, "amount_limit")
                    ->widget(
                        MaskedInput::class,
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
                        ]
                    ) ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'count_limit') ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'experience_limit') ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'request_again_limit') ?>
            </div>

            <div class="col-12 col-md-3">
                <?php $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; ?>
                <?= $form->field($model, 'month_limit')->widget(Select2::class, [
                    'data' => array_combine($months, array_map(fn ($m) => Jdate::getMonthNames($m), $months)),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'dir' => 'rtl',
                        'placeholder' => Yii::t('app', 'Select...'),
                        'multiple' => true
                    ],
                ]); ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'day_limit_start')->textInput(['type' => 'number']); ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'day_limit_end')->textInput(['type' => 'number']); ?>
            </div>

            <div class="col-12">
                <?= $form->field($model, 'salary_items_addition')
                    ->dropDownList(Comfort::itemAlias('SalaryItemsAddition'), ['prompt' => Yii::t('app', 'Select')]) ?>
            </div>

            <div class="col-12 col-md-6">
                <?= $form->field($model, 'related_faq')->widget(Select2::class, [
                    'data' => Faq::itemAlias('ListEmployee'),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ], 'options' => [
                        'placeholder' => 'آیین نامه تشریح شده',
                        'dir' => 'rtl',
                        'id' => 'related_faq',
                    ],
                ]); ?>
            </div>

            <div class="col-12 col-md-6 position-relative">
                <?= $form->field($model, 'related_faq_clause')->widget(DepDrop::class, [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => ['placeholder' => Yii::t('app', 'Related Faq Clause')],
                    'pluginOptions' => [
                        'loadingText' => Yii::t('app', 'Loading...'),
                        'depends' => ['related_faq'],
                        'placeholder' => Yii::t("app", "Select"),
                        'url' => Url::to(['/faq/clauses', 'selected' => $model->related_faq_clause]),
                        'initialize' => true,
                        'initDepends' => ['related_faq'],
                    ]
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'users')->widget(Select2::class, [
                    'data' => User::getUserWithRoles(['employee']),
                    'options' => [
                        'placeholder' => 'کاربرانی که مجاز به استفاده هستند',
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                ]); ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'excluded_users')->widget(Select2::class, [
                    'data' => User::getUserWithRoles(['employee']),
                    'options' => [
                        'placeholder' => 'کاربرانی که مجوز استفاده ندارند',
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                ]); ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'jobs')->widget(Select2::className(), [
                    'initValueText' => $initValueTextTagsInclude,
                    'options' => [
                        'placeholder' => Yii::t("app", "Search"),
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Error Loading'] . "'; }"),
                            'inputTooShort' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Input Too Short'] . "'; }"),
                            'loadingMore' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Loading More'] . "'; }"),
                            'noResults' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['No Results'] . "'; }"),
                            'searching' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Searching'] . "'; }"),
                            'maximumSelected' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Maximum Selected'] . "'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/tags/find', 'category' => Tags::MODEL_JOBS]),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                ]); ?>
            </div><div class="col-md-6">
                <?= $form->field($model, 'excluded_jobs')->widget(Select2::className(), [
                    'initValueText' => $initValueTextTagsExclude,
                    'options' => [
                        'placeholder' => Yii::t("app", "Search"),
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Error Loading'] . "'; }"),
                            'inputTooShort' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Input Too Short'] . "'; }"),
                            'loadingMore' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Loading More'] . "'; }"),
                            'noResults' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['No Results'] . "'; }"),
                            'searching' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Searching'] . "'; }"),
                            'maximumSelected' => new JsExpression("function () { return '" . Yii::t('app', 'Select2')['Maximum Selected'] . "'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/tags/find', 'category' => Tags::MODEL_JOBS]),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                ]); ?>
            </div>
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'married')->checkbox() ?>
            </div>

            <div class="col-12 col-md-4">
                <?= $form->field($model, 'document_required')->checkbox()->label('اجباری بودن بارگذاری مستندات برای ثبت درخواست') ?>
            </div>

            <div class="col-12 col-md-4">
                <?= $form->field($model, 'showcase')->checkbox() ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            </div>


        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>