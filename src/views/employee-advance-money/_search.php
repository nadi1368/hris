<?php

use hesabro\hris\models\AdvanceMoneySearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model AdvanceMoneySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="advance-money-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="card-body">
    <div class="row">
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'comment') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'creator_id') ?>

    <?php // echo $form->field($model, 'update_id') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'changed') ?>

		<div class="col align-self-center text-right">
			<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary']) ?>
		</div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
