<?php

use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\hris\models\SalaryItemsAddition;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryItemsAddition */
?>

<?php $form = ActiveForm::begin(['id' => 'salary-items-addition-form']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-12">
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

            <div class="col-md-4">
                <?= $form->field($model, 'type')->dropDownList(SalaryItemsAddition::itemAlias('TypeLeaveHourly'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col-md-8">
                <?= $form->field($model, 'range')->widget(dateRangePicker::class, [
                    'options'     => [
                        'locale'            => [
                            'format' => 'jYYYY/jMM/jDD HH:mm:ss',
                        ],
                        'drops'             => 'down',
                        'opens'             => 'right',
                        'jalaali'           => true,
                        'showDropdowns'     => true,
                        'language'          => 'fa',
                        'singleDatePicker'  => false,
                        'useTimestamp'      => true,
                        'timePicker'        => true,
                        'timePickerSeconds' => true,
                        'timePicker24Hour'  => true
                    ],
                    'htmlOptions' => [
                        'id'           => 'salaryitemsaddition-range',
                        'class'        => 'form-control',
                        'autocomplete' => 'off',
                    ]
                ]); ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>
            </div>

        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>