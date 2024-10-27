<?php
use hesabro\hris\models\EmployeeBranchUser;
use yii\bootstrap4\Nav;

/* @var $this yii\web\View */
/* @var $model EmployeeBranchUser */
?>
<?php
Nav::begin([
	'options' => [
		'id' => 'employee-branch-user-view-user-nav',
		'class' => 'nav nav-tabs',
	],
	'items' => [
		[
			'label' =>'اطلاعات کارمند',
			'url' => [
				'employee-branch/view-user',
				'branch_id' => $model->branch_id,
				'user_id' => $model->user_id,
			],
			'linkOptions' => [
				'id' => 'employeeInfoTab',
				'class' => 'nav-link ' . (Yii::$app->controller->action->id == 'view-user' ? 'active' : ''),
			],
		],
//		[
//			'label' => 'مدارک',
//			'url' => [
//				'employee-branch/view-user-documents',
//				'branch_id' => $model->branch_id,
//				'user_id' => $model->user_id,
//			],
//			'linkOptions' => [
//				'id' => 'employeeInfoTab',
//				'class' => 'nav-link ' . (Yii::$app->controller->action->id == 'view-user-documents' ? 'active' : ''),
//			],
//		],
		[
			'label' =>'قرارداد ها',
			'url' => [
				'user-contracts/employee-contracts',
				'branch_id' => $model->branch_id,
				'user_id' => $model->user_id,
			],
			'linkOptions' => [
				'id' => 'employeeContractTab',
				'class' => 'nav-link ' . (Yii::$app->controller->action->id == 'view-user-contract' ? 'active' : ''),
			],
		],
	],
]);

Nav::end();