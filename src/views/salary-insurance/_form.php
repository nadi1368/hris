<?php
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryInsurance */
/* @var $form yii\bootstrap4\ActiveForm */
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
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
