<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryBaseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="salary-base-search">

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

    <?= $form->field($model, 'year') ?>

    <?= $form->field($model, 'group') ?>

    <?= $form->field($model, 'creator_id') ?>

    <?= $form->field($model, 'update_id') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'changed') ?>

    <?php // echo $form->field($model, 'cost_of_year') ?>

    <?php // echo $form->field($model, 'cost_of_work') ?>

    <?php // echo $form->field($model, 'cost_of_hours') ?>

		<div class="col align-self-center text-right">
			<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
		</div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
