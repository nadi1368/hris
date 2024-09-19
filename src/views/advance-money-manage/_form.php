<?php

use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model backend\models\AdvanceMoney */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="advance-money-form">

    <?php $form = ActiveForm::begin(['id' => 'ajax-form-advance-money']); ?>
    <div class="card-body">
        <div class="row">

			<div class="col-md-6">
				<?= $form->field($model, 'user_id')->widget(Select2::class, [
					'data' => User::userOptions(),
					'options' => [
						'placeholder' => 'کاربر',
						'dir' => 'rtl',
					],
				]) ?>
			</div>

            <div class="col-md-6">
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
