<?php

/* @var $this yii\web\View */
/* @var $model \hesabro\hris\models\EmployeeContent */
/* @var string $title */
/* @var string|null $type */
/* @var bool $isTypeSet */
?>
<div class="faq-update card">
	<?= $this->render('_form', [
		'model' => $model,
        'type' => $type,
        'isTypeSet' => $isTypeSet
	]) ?>
</div>
