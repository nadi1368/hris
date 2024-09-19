<?php


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ContractTemplates */

$this->title = Yii::t('app', "Create $model->typeText Template");
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', "$model->typeText Templates"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-templates-create card">
	<?= $this->render('_form', [
		'model' => $model
	]) ?>
</div>
