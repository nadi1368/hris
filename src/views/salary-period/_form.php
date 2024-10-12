<?php

use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="salary-period-form">

    <?php $form = ActiveForm::begin(['id' => 'form-salary-period']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <?php if ($model->getScenario() == SalaryPeriod::SCENARIO_CREATE): ?>
                <div class="col-md-4">
                    <?= $form->field($model, 'start_date')->widget(MaskedInput::class, ['mask' => '9999/99',]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
