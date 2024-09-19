<?php

use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\EmployeeRequest;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var EmployeeRequest $model
 */

$form = ActiveForm::begin([
    'id' => 'form-employee-request-official-letter',
    'options' => ['data-pjax' => true,]
]);
?>

<div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'contract_template_id')->dropdownList(ContractTemplates::itemAlias('ListOfficialLetter') ?? [], ['prompt' => 'نوع نامه را انتخاب کنید ...']) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

