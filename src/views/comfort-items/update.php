<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortItems */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Comfort Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="comfort-items-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
