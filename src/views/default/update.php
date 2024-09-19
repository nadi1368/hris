<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */

$this->title = Yii::t('app','Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Employee Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="employee-branch-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
