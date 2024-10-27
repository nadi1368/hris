<?php


use hesabro\hris\models\RequestLeave;
use hesabro\hris\models\RequestLeaveSearch;
use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;


/* @var $this yii\web\View */
/* @var $model RequestLeaveSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<?php $form = ActiveForm::begin([
    'method' => 'get',
]); ?>
<div class="card-body">
    <div class="row">

        <div class="col-md-4">
            <?= $form->field($model, 'range')->widget(DateRangePicker::class, [
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
                    'id'           => 'requestleave-range1',
                    'class'        => 'form-control',
                    'autocomplete' => 'off',
                ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList(RequestLeave::itemAlias('Types'), ['prompt' => Module::t('module', 'Select...')]) ?>
        </div>
        <div class="col align-self-center text-right">
            <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary ']) ?>
            <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
