<?php

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContracts */

$this->title = Yii::t('app', 'Create User Contracts');
$this->params['breadcrumbs'][] = ['label' => $model->user->fullName, 'url' => ['user-contracts/employee-contracts', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = $model->id;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-contracts-update card">
    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
    ]) ?>
</div>
