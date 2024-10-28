<?php

use hesabro\hris\models\RequestLeave;
use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model RequestLeaveSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'method' => 'get',
]); ?>
<div class="card-body">
    <div class="row">

        <div class="col-md-3">
            <?= $form->field($model, 'user_id')->widget(Select2::className(), [
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
            <?= $form->field($model, 'range')->widget(DateRangePicker::classname(), [
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
                    'id'           => 'requestleave-range',
                    'class'        => 'form-control',
                    'autocomplete' => 'off',
                ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList(RequestLeave::itemAlias('Types'), ['prompt' => Module::t('module', 'Select...')]) ?>
        </div>
        <div class="col align-self-center text-right">
            <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary', 'name' => 'TypeReport', 'value' => false]) ?>
            <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary', 'name' => 'TypeReport', 'value' => false]) ?>    </div>
    </div>
    <div class="row">
        <div class="col text-right">
            <?= Html::submitButton(Module::t('module', 'Excel'), ['class' => 'btn btn-danger btn-flat', 'name' => 'TypeReport', 'value' => 'excel']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
