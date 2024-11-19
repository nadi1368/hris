<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\select2\Select2;
use hesabro\hris\models\EmployeeBranch;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchUser */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="deliver-shift-form">
    <?php $form = ActiveForm::begin([
        'id' => 'ajax-form-employee-user'
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'branch_id')->widget(Select2::class, [
                    'data' => EmployeeBranch::itemAlias('List'),
                    'options' => ['placeholder' => Yii::t("app", "Search")],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
