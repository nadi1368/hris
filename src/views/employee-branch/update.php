<?php

use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */

$this->title = Module::t('module','Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module','Employee Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-branch-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
