<?php

use hesabro\hris\models\RequestLeave;
use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model RequestLeave */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Pjax::begin([
    'timeout'         => false,
    'enablePushState' => false,
]) ?>
    <div class="request-leave-form">

        <?php $form = ActiveForm::begin([
            'id' => 'ajax-request-leave-form',
        ]); ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'type')->dropDownList(RequestLeave::itemAlias('TypesHourly'), [
                            'prompt' => Module::t('module', 'Select...'),
                            'disabled' => $model->isNewRecord ? false : true,
                    ]) ?>
                </div>
                <div class="col-md-6">
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
                            'id'           => 'requestleave-range',
                            'class'        => 'form-control',
                            'autocomplete' => 'off',
                        ]
                    ]); ?>
                </div>

                <div class="col-md-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php Pjax::end() ?>