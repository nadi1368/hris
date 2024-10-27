<?php

use hesabro\hris\models\RequestLeave;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model RequestLeave */

$this->title = Module::t('module', 'Create Request Leave');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Request Leaves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-leave-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
