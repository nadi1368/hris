<?php

use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\employee\models\EmployeeRollCallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-roll-call-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($model, 'fromDate')->widget(DateRangePicker::class, [
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
                        'id'           => 'employeerollcallsearch-from-date',
                        'class'        => 'form-control',
                        'autocomplete' => 'off',
                    ]
                ]); ?>
            </div>

			<div class="col-md-2">
				<?= $form->field($model, 'toDate')->widget(dateRangePicker::class, [
					'options' => [
						'locale' => [
							'format' => 'jYYYY/jMM/jDD',
						],
						'drops' => 'down',
						'opens' => 'right',
						'jalaali' => true,
						'showDropdowns' => true,
						'language' => 'fa',
						'singleDatePicker' => true,
						'useTimestamp' => true,
						'timePicker' => false,
						'timePickerSeconds' => true,
						'timePicker24Hour' => true
					],
					'htmlOptions' => [
						'id' => 'employeerollcallsearch-to-date',
						'class' => 'form-control',
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
