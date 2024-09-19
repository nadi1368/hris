<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */
?>
<div id="body-insurance-with-native">
    <?php $form = ActiveForm::begin([
        'id'=>'form-excel-insurance-with-native',
    ]); ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'DSK_KIND') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'DSK_LISTNO') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'DSK_NUM') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'DSK_TDD') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TROOZ")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TMAH")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TMAZ")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TMASH")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TTOTL")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TBIME")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_TKOSO")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_BIC")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_RATE")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_PRATE")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "DSK_BIMH")
                        ->widget(MaskedInput::className(),
                            [
                                'options' => [
                                    'autocomplete' => 'off',
                                ],
                                'clientOptions' => [
                                    'alias' => 'integer',
                                    'groupSeparator' => ',',
                                    'autoGroup' => true,
                                    'removeMaskOnSubmit' => true,
                                    'autoUnmask' => true,
                                ],
                            ]) ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'DSK_DISC')->textarea(['rows' => 1]) ?>
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