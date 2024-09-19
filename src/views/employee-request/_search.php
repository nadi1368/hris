<?php

use backend\models\User;
use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRequestSearch;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var EmployeeRequestSearch $model
 */
?>

<div>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-3">
                <?= $form->field($model, 'user_id')->widget(Select2::className(), [
                    'data' => User::getUserWithRoles(['employee']),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'placeholder' => Yii::t('app', 'Search'),
                        'dir' => 'rtl',
                    ],
                ]); ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'type')->dropdownList(EmployeeRequest::itemAlias('Type'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropdownList(EmployeeRequest::itemAlias('Status'), ['prompt' => Yii::t('app', 'Select...')]) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
