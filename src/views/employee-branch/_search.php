<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-branch-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="card-body">
    <div class="row">
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'manager') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'creator_id') ?>

    <?php // echo $form->field($model, 'update_id') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'changed') ?>

		<div class="col align-self-center text-right">
			<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
		</div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
