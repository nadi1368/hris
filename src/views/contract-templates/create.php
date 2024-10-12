<?php


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ContractTemplates */

use hesabro\hris\Module;

$this->title = Module::t('module', "Create $model->typeText Template");
$this->params['breadcrumbs'][] = ['label' => Module::t('module', "$model->typeText Templates"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-templates-create card">
	<?= $this->render('_form', [
		'model' => $model
	]) ?>
</div>
