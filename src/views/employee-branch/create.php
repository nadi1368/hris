<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */

$this->title = Module::t('module','Create');
$this->params['breadcrumbs'][] = ['label' => Module::t('module','Employee Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-branch-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
