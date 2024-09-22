<?php

use hesabro\hris\models\EmployeeBranchUser;
use common\models\UserUpload;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $model EmployeeBranchUser */
/* @var $userUploadModel UserUpload */
/* @var $list UserUpload[] */

$this->title = $model->user->fullName;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
	<div class="card-header">
		<?= $this->render('_view_user_nav', [
			'model' => $model,
		]) ?>
	</div>

	<div class="card-body">
		<?php Pjax::begin(['id' => 'employee-documents-pjax']) ?>
			<?= $this->render('_user_documents', [
				'model' => $model,
				'list' => $list,
				'userUploadModel' => $userUploadModel,
			]) ?>
		<?php Pjax::end() ?>
	</div>
</div>

