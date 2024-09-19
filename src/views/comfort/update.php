<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\Comfort */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Comforts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="comfort-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
