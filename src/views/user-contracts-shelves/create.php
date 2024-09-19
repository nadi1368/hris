<?php


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContractsShelves */

$this->title = Yii::t('app', 'Create User Contracts Shelves');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Contracts Shelves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contracts-shelves-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
