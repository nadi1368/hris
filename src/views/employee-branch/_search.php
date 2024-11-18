<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-branch-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'id') ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'title') ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Module::t('module','Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Module::t('module','Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
