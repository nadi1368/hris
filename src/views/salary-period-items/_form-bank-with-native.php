<?php

use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */
?>
<div id="body-excel-bank-with-native">
    <?php $form = ActiveForm::begin([
        'id' => 'form-excel-bank-with-native',
    ]); ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'shaba')->widget(MaskedInput::class, [
                        'mask' => 'IR99-9999-9999-9999-9999-9999-99',
                        'options' => ['class' => 'form-control ltr'],
                        'clientOptions' => [
                            'removeMaskOnSubmit' => true,
                        ]
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'bank_name') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'file_number') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'another_period')->widget(Select2::classname(), [
                        'data' => $model->getAnotherPeriodList(),
                        'options' => [
                            'placeholder' => Yii::t("app", "Search"),
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
            <?= Html::submitButton(Yii::t('app', 'Get'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>