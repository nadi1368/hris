<?php

use hesabro\hris\models\ComfortItems;
use common\models\Comments;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var View $this
 * @var ComfortItems $model
 * @var ActiveForm $form
 * @var Comments $comment
 */

$standalone = !isset($form);
$form = $standalone ? ActiveForm::begin(['id'=>'comfort-items-refer-form']) : $form;
?>

<div class="row">
    <div class="col-12">
        <?= $form->field($comment, 'owner')->widget(Select2::class, [
            'data' => Module::getInstance()->user::userOptions(),
            'options' => [
                'placeholder' => Module::t('module', 'Users'),
                'dir' => 'rtl',
                'multiple' => true
            ]
        ]); ?>
    </div>

    <div class="col-12 col-md-6">
        <?= $form->field($comment, 'css_class')->dropDownList(Comments::itemAlias('Type'), [
            'value' => Comments::TYPE_DANGER
        ]) ?>
    </div>

    <div class="col-12 col-md-6 date-input">
        <?= $form->field($comment, 'due_date')
            ->widget(MaskedInput::class, [
                'mask' => '9999/99/99',
                'options' => [
                    'autocomplete' => 'off'
                ]
            ])
        ?>
    </div>
    <div class="col-md-12 d-flex align-items-center justify-content-start" style="gap: 12px">
        <?= $form->field($comment, 'send_email')->checkbox() ?>
        <?= $form->field($comment, 'send_sms')->checkbox() ?>
    </div>
    <div class="col-12">
        <?= $form->field($comment, 'des')->textarea(['rows' => 6]) ?>
    </div>

    <?php if($standalone): ?>
        <div class="col-12">
            <?= Html::submitButton(Module::t('module', 'Refer'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php endif; ?>
</div>

<?php $standalone && ActiveForm::end() ?>

