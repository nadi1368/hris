<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\WorkshopInsurance */

$this->title = Yii::t('app', 'Create Workshop Insurance');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Workshop Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workshop-insurance-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
