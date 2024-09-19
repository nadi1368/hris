<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryBase */

$this->title = Yii::t('app', 'Create Salary Base');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Salary Bases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salary-base-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
