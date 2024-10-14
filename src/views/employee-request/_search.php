<?php

use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRequestSearch;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var EmployeeRequestSearch $model
 */
?>

<div>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-3">
                <?= $form->field($model, 'user_id')->widget(Select2::class, [
                    'data' => Module::getInstance()->user::getUserWithRoles(Module::getInstance()->employeeRole),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'placeholder' => Module::t('module', 'Search'),
                        'dir' => 'rtl',
                    ],
                ]); ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'type')->dropdownList(EmployeeRequest::itemAlias('Type'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropdownList(EmployeeRequest::itemAlias('Status'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
