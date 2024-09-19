<?php


use hesabro\hris\models\EmployeeBranchUser;
use common\models\UserUpload;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $model EmployeeBranchUser */

/* @var $this yii\web\View */
/* @var $model EmployeeBranchUser */
?>
<?php Pjax::begin(['id' => 'view-user-info-pjax']) ?>
<div class="card">

    <div class="card-body">
        <div class="row">
            <?php foreach ($model->getInsuranceData() as $attribute => $data): ?>
                <div class="col-md-4 my-3">
                    <?= $model->getAttributeLabel($attribute) . ' : ' . '<span class="text-bold">' . $data . '</span>' ?>
                </div>
            <?php endforeach; ?>
            <div class="col-md-8 my-3">
                <?= ($model->account !== null ? $model->account->getLink() : '<span class="text-danger">حساب تفضیل کارمند ست نشده است.لطفا از قسمت بروزرسانی ثبت نمایید</span>') ?>
            </div>
        </div>

        <?php if (is_array($model->history)): ?>
            <h5 class="text-center">سابقه قبلی</h5>
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th></th>
                    <th>تاریخ شروع به کار</th>
                    <th>تاریخ ترک کار</th>
                    <th>سند حسابدرای</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->history as $index => $history): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $history->start_work ?></td>
                        <td><?= $history->end_work ?></td>
                        <td><?= Html::a($history->document_id_end_work, ['/document/view', 'id' => $history->document_id_end_work], ['class' => 'text-info showModalButton', 'data-size' => 'modal-xl', 'title' => Yii::t('app', 'Document')]) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <?= $model->canSetEndWork() ? Html::a(Yii::t('app', 'Set End Work'),
            'javascript:void(0)', [
                'title' => Yii::t('app', 'Set End Work'),
                'id' => 'set-end-work' . $model->user_id,
                'class' => 'btn btn-primary',
                'data-size' => 'modal-lg',
                'data-title' => Yii::t('app', 'Set End Work'),
                'data-toggle' => 'modal',
                'data-target' => '#modal-pjax',
                'data-url' => Url::to(['set-end-work', 'id' => $model->user_id]),
                'data-reload-pjax-container-on-show' => false,
                'data-hide-previous-modal' => '#modal',
            ]) : (($document = $model->getDocumentEndWork()) !== null ? Html::a('مشاهده سند تسویه', ['/document/view', 'id' => $document->id], ['class' => 'btn btn-success showModalButton', 'data-size' => 'modal-xl', 'title' => Yii::t('app', 'Document')]) : ''); ?>

        <?= $model->canStartWorkAgain() ? Html::a(Yii::t('app', 'Start Work Again'),
            'javascript:void(0)', [
                'title' => Yii::t('app', 'Start Work Again'),
                'id' => 'Return-end-work' . $model->user_id,
                'class' => 'btn btn-danger p-jax-btn',
                'data-pjax' => '0',
                'data-url' => Url::to(['start-work-again', 'id' => $model->user_id]),
                'data-reload-pjax-container' => 'view-user-info-pjax',
                'data-method' => 'post'
            ]) : ''; ?>
        <?= $model->canUpdate() ? Html::a(Yii::t('app', 'Update'),
            'javascript:void(0)', [
                'title' => Yii::t('app', 'Update'),
                'id' => 'update-user' . $model->user_id,
                'class' => 'btn btn-primary',
                'data-size' => 'modal-xl',
                'data-title' => Yii::t('app', 'Update'),
                'data-toggle' => 'modal',
                'data-target' => '#modal-pjax',
                'data-url' => Url::to(['update-user', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                'data-reload-pjax-container-on-show' => false,
                'data-hide-previous-modal' => '#modal',
                'data-handle-form-submit' => 1,
                'disabled' => true,
            ]) : '';
        ?>
        <?= $model->canUpdate() ? Html::a(Yii::t('app', 'Insurance Data'),
            'javascript:void(0)', [
                'title' => Yii::t('app', 'Insurance Data'),
                'id' => 'insurance-data' . $model->user_id,
                'class' => 'btn btn-primary',
                'data-size' => 'modal-xl',
                'data-title' => Yii::t('app', 'Insurance Data'),
                'data-toggle' => 'modal',
                'data-target' => '#modal-pjax',
                'data-url' => Url::to(['insurance-data', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                'data-reload-pjax-container-on-show' => false,
                'data-hide-previous-modal' => '#modal',
                'data-handle-form-submit' => 1,
                'disabled' => true,
            ]) : '' ?>
        <?= Html::a('اطلاعات کاربری', ['/user-main/view-ajax', 'id' => $model->user_id], ['class' => 'btn btn-success showModalButton', 'data-pjax' => '0', 'title' => $model->user->fullName]) ?>
        <?= Html::a('دروه حقوق های قبلی', ['salary-period-items/user', 'id' => $model->user_id], ['class' => 'btn btn-info', 'title' => 'مشاهده دروه حقوق های قبلی این کارمند']
        ) ?>
        <?= Html::a(Yii::t('app', 'Log'), ['/mongo/log/view-ajax', 'modelId' => $model->user_id, 'modelClass' => EmployeeBranchUser::class], ['class' => 'btn btn-secondary showModalButton', 'data-pjax' => '0', 'title' => Yii::t('app', 'Log')]) ?>
    </div>
</div>
<?php Pjax::end() ?>
