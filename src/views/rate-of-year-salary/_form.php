<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\RateOfYearSalary */
/* @var $form ActiveForm */
?>

<div class="rate-of-year-salary-form">

    <?php $form = ActiveForm::begin(['id' => 'rate-of-year-form']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'year')->textInput() ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, "rate_of_day")
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
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
