<?php


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsShelves */

use hesabro\hris\Module;

$this->title = Module::t('module', 'Create User Contracts Shelves');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'User Contracts Shelves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contracts-shelves-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
