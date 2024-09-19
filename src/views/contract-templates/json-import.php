<?php


/* @var $this \yii\web\View */
/* @var $model  */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

?>

<div class="contract-templates-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-contract-templates-json-import',
        'options' => ['data-pjax' => true,],
    ]); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-12">
                <?= $form->field($model, 'json_file')->fileInput() ?>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>