<?php

use hesabro\hris\models\EmployeeBranchUser;
use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\InternalNumber */
/* @var $modelUser EmployeeBranchUser */
/* @var $form yii\bootstrap4\ActiveForm */

$form = ActiveForm::begin(['id' => 'internal-number-form']); ?>
<div class="card-body">
    <div class="row">

        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'number')->textInput() ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'job_position')->textInput() ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'user_id')->widget(Select2::class, [
                'data' => User::getUserWithRoles(['user']),
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'options' => [
                    'placeholder' => Yii::t('app', 'Search'),
                    'dir' => 'rtl',
                ],
            ]); ?>
        </div>

    </div>
</div>
<div class="card-footer">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>