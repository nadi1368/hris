<?php

use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\UserContracts */

$this->title = Module::t('module', 'Create User Contracts');
$this->params['breadcrumbs'][] = ['label' => $model->user->fullName, 'url' => ['user-contracts/employee-contracts', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = $model->id;
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="user-contracts-update card">
    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
    ]) ?>
</div>
