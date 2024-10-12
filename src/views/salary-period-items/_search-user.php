<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\hris\models\SalaryPeriod;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriodItemsSearch */
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
            <div class="col-md-3">
                <?= $form->field($model, 'kind')->dropdownList(SalaryPeriod::itemAlias('Kind'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

