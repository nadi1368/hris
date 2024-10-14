<?php

use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchUser */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="salary-period-items-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">

                <?= $form->field($model, "user_id")->widget(Select2::class, [
                    'data' => Module::getInstance()->user::getUserWithRoles(Module::getInstance()->employeeRole),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'dir' => 'rtl',
                        'placeholder' => Module::t('module', 'Select...'),
                    ],
                ]); ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
