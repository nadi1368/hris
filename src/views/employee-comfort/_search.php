<?php

use hesabro\hris\models\Comfort;
use hesabro\hris\models\ComfortSearch;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var ComfortSearch $model
 */

$form = ActiveForm::begin([
    'action' => ['employee-comfort/index'],
    'method' => 'get',
]);
?>

<div class="card rounded-top">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-10">
                <?= $form->field($model, 'type')->dropdownList(['' => Module::t('module', 'All')] + Comfort::itemAlias('TypeCat'))?>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end justify-content-end" style="padding-bottom: 15px">
                <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
