<?php

use hesabro\hris\models\AdvanceMoney;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model AdvanceMoney */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="advance-money-form">

    <?php $form = ActiveForm::begin(['id' => 'ajax-form-advance-money']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, "amount")
                    ->widget(MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ])
                ?>
            </div>

			<div class="col-md-8">
				<?= $form->field($model, 'iban')->widget(MaskedInput::className(), [
					'mask' => 'IR99-9999-9999-9999-9999-9999-99',
					'options' => ['class' => 'form-control ltr'],
					'clientOptions' => [
						'removeMaskOnSubmit' => true,
					]
				]) ?>
			</div>

            <div class="col-md-12">
                <?= $form->field($model, 'comment')->textarea(['rows' => 2]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
