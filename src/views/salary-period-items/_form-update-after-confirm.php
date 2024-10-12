<?php

use hesabro\hris\models\SalaryBase;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $model hesabro\hris\models\SalaryPeriodItems */
/* @var $form yii\bootstrap4\ActiveForm */

?>

<div class="salary-period-items-form">

    <?php $form = ActiveForm::begin(['id' => 'form-salary-period-items']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'treatment_day')->hint(SalaryPeriodItems::itemAlias('HintLabel', 'treatment_day')) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'descriptionShowEmployee')->textarea(['rows' => 2]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
