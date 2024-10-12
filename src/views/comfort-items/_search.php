<?php

use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\hris\models\ComfortItems;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortItemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comfort-items-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'user_id')->widget(Select2::class, [
                    'data' => Module::getInstance()->user::getUserWithRoles(['employee']),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'placeholder' => Module::t('module', 'Search'),
                        'dir' => 'rtl',
                    ],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'status')->dropDownList(ComfortItems::itemAlias('Status'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
