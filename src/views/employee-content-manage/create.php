<?php


/* @var $this yii\web\View */
/* @var $model common\models\Faq */
/* @var string $title */
/* @var string|null $type */
/* @var bool $isTypeSet */
?>
<div class="faq-create card">
	<?= $this->render('_form', [
		'model' => $model,
        'type' => $type,
        'isTypeSet' => $isTypeSet
	]) ?>
</div>
