<?php

use common\components\Helper;
use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriodItemsSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="salary-period-items-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">

                <?= $form->field($model, "user_id")->widget(Select2::class, [
                    'data' => User::getUserWithRoles(['employee']),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'dir' => 'rtl',
                        'placeholder' => Yii::t('app', 'Select...'),
                        'multiple' => true
                    ],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'can_payment')->dropdownList(Helper::itemAlias('CheckboxTitle'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'check_advance_money')->checkbox() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'check_document')->checkbox() ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
