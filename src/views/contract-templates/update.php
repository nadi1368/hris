<?php

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ContractTemplates */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-templates-update card">
	<?= $this->render('_form', [
		'model' => $model
	]) ?>
</div>
