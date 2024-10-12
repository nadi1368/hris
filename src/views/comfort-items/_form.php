<?php

use common\models\Comments;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var hesabro\hris\models\ComfortItems $model
 * @var yii\widgets\ActiveForm $form
 * @var Comments $comment
 */

?>

<div class="comfort-items-form">

    <?php $form = ActiveForm::begin(['id' => 'comfort-items-comfirm-form']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
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
                        ]) ?>
            </div>

            <div class="col-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>
            </div>

            <div class="col-12">
                <?= $form->field($model, 'saveAdvanceMoney')->checkbox() ?>
            </div>

            <?php if (!$model->getComments()->count()): ?>

                <div class="position-relative pt-3 col-12" style="border: 3px dotted #bbbbbb; border-radius: 8px;">
                    <label class="position-absolute"
                           style="top: -14px; right: 16px; font-size: 18px; background: white; padding: 0 4px;"><?= Module::t('module', 'Refer') ?></label>
                    <?= $this->renderFile('@backend/modules/employee/views/comfort-items/_refer.php', [
                        'model' => $model,
                        'comment' => $comment,
                        'form' => $form
                    ]) ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Confirm'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
