<?php

use hesabro\hris\models\RequestLeave;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model RequestLeave */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Request Leaves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="request-leave-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
