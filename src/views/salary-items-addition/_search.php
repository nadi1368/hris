<?php

use hesabro\hris\Module;
use kartik\select2\Select2;
use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\hris\models\SalaryItemsAddition;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryItemsAdditionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="salary-items-addition-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
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

            <div class="col-md-2">
                <?= $form->field($model, 'kind')->dropDownList(SalaryItemsAddition::itemAlias('Kind'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'type')->dropDownList(SalaryItemsAddition::itemAlias('Type'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'status')->dropDownList(SalaryItemsAddition::itemAlias('Status'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col-md-2 date-input">
                <?= $form->field($model, 'from_date')->widget(dateRangePicker::class, [
                    'options'     => [
                        'locale'            => [
                            'format' => 'jYYYY/jMM/jDD',
                        ],
                        'drops'             => 'down',
                        'opens'             => 'right',
                        'jalaali'           => true,
                        'showDropdowns'     => true,
                        'language'          => 'fa',
                        'singleDatePicker'  => true,
                        'useTimestamp'      => true,
                        'timePicker'        => false,
                        'timePickerSeconds' => true,
                        'timePicker24Hour'  => true
                    ],
                    'htmlOptions' => [
                        'id'           => 'salaryitemsadditionsearch-from_date',
                        'class'        => 'form-control',
                        'autocomplete' => 'off',
                    ]
                ]); ?>
            </div>
            <div class="col-md-2 date-input">
                <?= $form->field($model, 'to_date')->widget(dateRangePicker::class, [
                    'options'     => [
                        'locale'            => [
                            'format' => 'jYYYY/jMM/jDD',
                        ],
                        'drops'             => 'down',
                        'opens'             => 'right',
                        'jalaali'           => true,
                        'showDropdowns'     => true,
                        'language'          => 'fa',
                        'singleDatePicker'  => true,
                        'useTimestamp'      => true,
                        'timePicker'        => false,
                        'timePickerSeconds' => true,
                        'timePicker24Hour'  => true
                    ],
                    'htmlOptions' => [
                        'id'           => 'salaryitemsadditionsearch-to_date',
                        'class'        => 'form-control',
                        'autocomplete' => 'off',
                    ]
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
