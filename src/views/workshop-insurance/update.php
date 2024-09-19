<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\WorkshopInsurance */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Workshop Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="workshop-insurance-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
