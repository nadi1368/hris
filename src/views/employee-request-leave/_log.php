<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use hesabro\hris\models\RequestLeave;
use backend\models\LogStatus;

/* @var $this yii\web\View */
/* @var RequestLeave $model */
?>
<table class="table table-view text-center">
    <thead>
    <tr>
        <th></th>
        <th><?= Module::t('module','From Status') ?></th>
        <th><?= Module::t('module','To Status') ?></th>
        <th><?= Module::t('module','Created') ?></th>
        <th><?= Module::t('module','Creator ID') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($model->getLogs()->orderBy(['id'=>SORT_DESC])->all() as $index=>$log): ?>
        <?php /** @var $log LogStatus */ ?>
        <tr class="<?= RequestLeave::itemAlias('StatusClass', $log->new_status) ?>">
            <td><?= $index+1 ?></td>
            <td><?= RequestLeave::itemAlias('Status', $log->status) ?></td>
            <td><?= RequestLeave::itemAlias('Status', $log->new_status) ?></td>
            <td><?= Yii::$app->jdate->date("Y/m/d H:i:s",$log->created) ?></td>
            <td><?= $log->creator->username ?></td>
        </tr>
        <?php if($log->comment): ?>
            <tr>
                <td colspan="5">
                    <p class="text-left"><?= Html::encode($log->comment) ?></p>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
