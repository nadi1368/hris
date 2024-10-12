<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortItems */

$this->title = Module::t('module', 'Create Comfort Items');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Comfort Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comfort-items-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
