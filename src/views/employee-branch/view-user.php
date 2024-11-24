<?php
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranchUser */
/* @var $searchModel hesabro\hris\models\UserContractsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->user->fullName;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'user-data-pjax', 'enablePushState' => false]); ?>
<div class="user-contracts-index card">
    <div class="card-header">
        <?= $this->renderFile('@hesabro/hris/views/employee-branch/_view_user_nav.php', [
            'model' => $model,
        ]) ?>
    </div>

    <div class="card-body">
        <?= $this->renderFile('@hesabro/hris/views/employee-branch/_view-user.php', [
            'model' => $model,
        ]) ?>
    </div>
</div>
<?php Pjax::end(); ?>
