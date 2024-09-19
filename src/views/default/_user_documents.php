<?php

use common\models\UserUpload;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;


/* @var $this View */
/* @var $list UserUpload */
/* @var $userUploadModel UserUpload */

?>

<div class="card">
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
	<div class="card-body">

		<?= $form->field($userUploadModel, 'type')->dropDownList(UserUpload::itemAlias('Type'), ['prompt' => Yii::t("app", "Select...")]); ?>

		<?= $form->field($userUploadModel, 'file_name')->fileInput() ?>

	</div>
	<div class="card-footer">
		<?= Html::submitButton(Yii::t("app", "Upload"), ['class' => 'btn btn-success btn btn-flat']); ?>
	</div>
</div>


<?php ActiveForm::end() ?>


<div class="table-responsive">
	<table class="table table-striped ">
		<thead>
		<tr>
			<th>#</th>
			<th><?= $userUploadModel->getAttributeLabel('type') ?></th>
			<th><?= $userUploadModel->getAttributeLabel('file_name') ?></th>
			<th><?= $userUploadModel->getAttributeLabel('status') ?></th>
			<th><?= $userUploadModel->getAttributeLabel('confirm_by') ?></th>
			<th><?= $userUploadModel->getAttributeLabel('created') ?></th>
			<th><?= $userUploadModel->getAttributeLabel('creator_id') ?></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($list as $index => $item): ?>
			<tr>
				<td><?= $index + 1 ?></td>
				<td><?= UserUpload::itemAlias('Type', $item->type) ?></td>
				<td><?php
					$img = Html::img($item->getSrc(), ['class' => 'img-responsive', 'width' => '200px']);
					echo Html::a($img, ['view-attach', 'id' => $item->id], ['class' => 'showModalButton']);
					?></td>
				<td><?= '<label class="badge badge-' . UserUpload::itemAlias('StatusClass', $item->status) . '">' . UserUpload::itemAlias('Status', $item->status) . '</label>' ?></td>
				<td><?= $item->confirm_by ? $item->confirm->getLink() : '' ?></td>
				<td><?= '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $item->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $item->created) . '</span>' ?></td>
				<td><?= '<span title="بروز رسانی شده توسط ' . $item->update->fullName . '">' . $item->creator->fullName . '</span>'; ?></td>
				<td class="grid-view">
					<?= $item->status == UserUpload::STATUS_ACTIVE ? Html::a('<span class="fa fa-check"></span>', ['confirm-document', 'id' => $item->id], [
						'class' => 'grid-btn grid-btn-update ajax-btn',
						'data-url' => Url::to(['confirm-document', 'id' => $item->id]),
						'data-confirm' => Yii::t('app', 'Are you sure?'),
						'data-view' => 'index',
						'data-p-jax' => '#employee-documents-pjax',
						'data-method' => 'post',
					]) : ''; ?>
					<?= $item->canDelete() ? Html::a('<span class="far fa-trash-alt text-danger"></span>', ['delete-document', 'id' => $item->id], [
						'title' => Yii::t('yii', 'Delete'),
						'class' => 'ajax-btn',
						'data-url' => Url::to(['delete-document', 'id' => $item->id]),
						'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
						'data-view' => 'index',
						'data-p-jax' => '#employee-documents-pjax',
						'data-method' => 'post',
					]) : ''; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
