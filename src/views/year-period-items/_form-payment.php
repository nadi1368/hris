<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriod */
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-payment',
    'method' => 'post',
]); ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 date-input">
                <?= $form->field($model, 'payment_date')->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                ]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
        <?= Html::submitButton(Yii::t('app', 'Payment'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
