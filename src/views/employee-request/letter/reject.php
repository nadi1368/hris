<?php
use hesabro\hris\models\Letter;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/**
* @var Letter $relatedModel
*/
$form = ActiveForm::begin(['id' => 'reject-letter-form']);
?>

<div class="card mb-0">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <p><?= Module::t('module', 'Are you sure you want to reject this item?') ?></p>
            </div>
            <div class="col-12">
                <?= $form->field($relatedModel, 'rejectDescription')->textarea() ?>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-end">
        <?= Html::submitButton(Module::t('module', 'Reject') . ' ' . Module::t('module', 'Request'), ['class' => 'btn btn-danger']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>