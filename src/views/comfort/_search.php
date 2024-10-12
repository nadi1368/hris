<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comfort-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
<div class="card-body">
    <div class="row">
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'expire_time') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'type_limit') ?>

    <?php // echo $form->field($model, 'amount_limit') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'additional_data') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'creator_id') ?>

    <?php // echo $form->field($model, 'update_id') ?>

    <?php // echo $form->field($model, 'changed') ?>

		<div class="col align-self-center text-right">
			<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
		</div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
