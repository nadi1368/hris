<?php

use common\models\User;
use kartik\select2\Select2;
use common\widgets\dateRangePicker\dateRangePicker;
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
                <?= $form->field($model, 'user_id')->widget(Select2::className(), [
                    'data' => User::getUserWithRoles(['employee']),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'placeholder' => Yii::t('app', 'Search'),
                        'dir' => 'rtl',
                    ],
                ]); ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'type')->dropDownList(SalaryItemsAddition::itemAlias('TypeLow'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'second')->textInput() ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'date')->widget(dateRangePicker::classname(), [
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
                        'id'           => 'salaryitemsaddition-date',
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
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>