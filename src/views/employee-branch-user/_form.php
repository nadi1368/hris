<?php

use hesabro\hris\widgets\UserSelect2;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\Module;
use hesabro\hris\widgets\EmployeeBranchSelect2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var EmployeeBranchUser $model
 */

$form = ActiveForm::begin(['id' => 'employee-hiring-form']);
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'user_id')->widget(UserSelect2::class) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'branch_id')->widget(EmployeeBranchSelect2::class) ?>
            </div>
            <div class="col-12">
                <?= Html::submitButton(Module::t('module', 'Hiring'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
