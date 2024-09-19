<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortItems */

$this->title = Yii::t('app', 'Create Comfort Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Comfort Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comfort-items-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
