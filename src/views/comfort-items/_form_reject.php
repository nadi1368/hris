<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \yii\base\Model */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<?php Pjax::begin([
    'timeout' => false,
    'enablePushState' => false,
]) ?>
    <div class="credit-form">
        <?php $form = ActiveForm::begin([
            'id' => 'reject-form-contract',
            'options' => ['data-pjax' => true,]
        ]); ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <?= Html::submitButton(Module::t('module', 'Save'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php Pjax::end() ?>