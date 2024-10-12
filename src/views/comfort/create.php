<?php

use hesabro\hris\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\Comfort */

$this->title = Module::t('module', 'Create Comfort');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Comforts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comfort-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
