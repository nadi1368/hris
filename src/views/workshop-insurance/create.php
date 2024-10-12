<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\WorkshopInsurance */

$this->title = Module::t('module', 'Create Workshop Insurance');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Workshop Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workshop-insurance-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
