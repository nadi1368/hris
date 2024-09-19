<?php

use common\models\Tags;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryInsurance */
/* @var $form yii\bootstrap4\ActiveForm */

$tagModels = $model->tag_id ? Tags::find()->andWhere(['id' => $model->tag_id])->all() : [];
?>

<div class="salary-insurance-form">

    <?php $form = ActiveForm::begin(['id'=>'form-salary-insurance']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'group')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'tag_id')->widget(Select2::class, [
                    'initValueText' => ArrayHelper::map($tagModels, 'id', 'title'),
                    'options' => [
                        'placeholder' => Yii::t("app", "Search"),
                        'dir' => 'rtl'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("() => 'خطا در جستجوی اطلاعات'"),
                            'inputTooShort' => new JsExpression("() => 'لطفا تایپ نمایید'"),
                            'loadingMore' => new JsExpression("() => 'بارگیری بیشتر'"),
                            'noResults' => new JsExpression("() => 'نتیجه ای یافت نشد.'"),
                            'searching' => new JsExpression("() => 'در حال جستجو...'"),
                            'maximumSelected' => new JsExpression("() => 'حداکثر انتخاب شده'"),
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
                ])->label(Tags::getLabelWithBtnCreate(
                    $model->getAttributeLabel('tag_id'),
                    Yii::t('app', 'Create') . ' ' . Yii::t('app', 'Job'),
                    Tags::MODEL_JOBS
                )) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
