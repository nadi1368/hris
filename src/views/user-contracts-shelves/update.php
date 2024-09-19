<?php

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsShelves */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Contracts Shelves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-contracts-shelves-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
