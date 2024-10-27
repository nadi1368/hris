<?php

use hesabro\hris\models\RequestLeave;
use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
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
                    <?= $form->field($model, 'type')->dropDownList(RequestLeave::itemAlias('TypesDaily'), [
                        'disabled' => $model->isNewRecord ? false : true,
                        'prompt'   => Module::t('module', 'Select...')
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'range')->widget(DateRangePicker::class, [
                        'options'     => [
                            'locale'            => [
                                'format' => 'jYYYY/jMM/jDD',
                            ],
                            'drops'             => 'down',
                            'opens'             => 'right',
                            'jalaali'           => true,
                            'showDropdowns'     => true,
                            'language'          => 'fa',
                            'singleDatePicker'  => false,
                            'useTimestamp'      => false,
                            'timePicker'        => false,
                            'timePickerSeconds' => false,
                            'timePicker24Hour'  => false
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