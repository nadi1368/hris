<?php

use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */
?>
<div id="body-excel-bank-with-native">
    <?php $form = ActiveForm::begin([
        'id' => 'form-excel-bank-non-cash',
    ]); ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'another_period')->widget(Select2::class, [
                        'data' => $model->getAnotherPeriodList(),
                        'options' => [
                            'placeholder' => Module::t('module', "Search"),
                            'multiple' => true,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
            <?= Html::submitButton(Module::t('module', 'Get'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>