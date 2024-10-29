<?php

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\helpers\widgets\FileInput;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var EmployeeBranchUser $model
 * @var View $this
 * @var ActiveForm $form
 * @var bool $isAdmin
 *
 */

$maxFileSize = EmployeeBranchUser::MAX_FILE_SIZE / 1024 . 'KB';
$this->beginBlock('documents');
$galleryIndex = 0;
?>
<div class="card-body">
    <div class="row">
        <div class="col-12 col-md-4">
            <?php $shPictureFirst = $model->getFileUrl('sh_picture_first'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('sh_picture_first') ?></label>
                <?php if ($shPictureFirst): ?>
                <div class="document-toolbar">
                    <a href="<?= $shPictureFirst ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                        <i class="fas fa-cloud-download-alt"></i>
                    </a>
                    <a href="<?= $shPictureFirst ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </div>

                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$shPictureFirst) {
                echo $form->field($model, 'sh_picture_first')->widget(FileInput::class, [
                    'defaultFile' => $shPictureFirst
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));
            } else {
                echo Html::tag(
                    'div',
                    $shPictureFirst ? Html::tag('img', options: ['src' => $shPictureFirst, 'alt' => 'sh_picture_first', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $shPictureSecond = $model->getFileUrl('sh_picture_second'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('sh_picture_second') ?></label>
                <?php if ($shPictureSecond): ?>
                    <div class="document-toolbar">
                        <a href="<?= $shPictureSecond ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $shPictureSecond ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$shPictureSecond) {
                echo $form->field($model, 'sh_picture_second')->widget(FileInput::class, [
                    'defaultFile' => $shPictureSecond
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));
            } else {
                echo Html::tag(
                    'div',
                    $shPictureSecond ? Html::tag('img', options: ['src' => $shPictureSecond, 'alt' => 'sh_picture_second', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $shPictureThird = $model->getFileUrl('sh_picture_third'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('sh_picture_third') ?></label>
                <?php if ($shPictureThird): ?>
                    <div class="document-toolbar">
                        <a href="<?= $shPictureThird ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $shPictureThird ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$shPictureThird) {
                echo $form->field($model, 'sh_picture_third')->widget(FileInput::class, [
                    'defaultFile' => $model->getFileUrl('sh_picture_third')
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));
            } else {
                echo Html::tag(
                    'div',
                    $shPictureThird ? Html::tag('img', options: ['src' => $shPictureThird, 'alt' => 'sh_picture_third', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div class="col-12 py-4">
            <?= $this->render('../../layouts/_divider', [
                'title' => implode(' ', [
                    Module::t('module', 'Photo'),
                    Module::t('module', 'ID Card'),
                    Module::t('module', 'And'),
                    Module::t('module', 'Military Service'),
                ])
            ]) ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $idCardFront = $model->getFileUrl('id_card_front'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('id_card_front') ?></label>
                <?php if ($idCardFront): ?>
                    <div class="document-toolbar">
                        <a href="<?= $idCardFront ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $idCardFront ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$idCardFront) {
                echo $form->field($model, 'id_card_front')->widget(FileInput::class, [
                    'defaultFile' => $model->getFileUrl('id_card_front')
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));
            } else {
                echo Html::tag(
                    'div',
                    $idCardFront ? Html::tag('img', options: ['src' => $idCardFront, 'alt' => 'id_card_front', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $idCardBack = $model->getFileUrl('id_card_back'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('id_card_back') ?></label>
                <?php if ($idCardBack): ?>
                    <div class="document-toolbar">
                        <a href="<?= $idCardBack ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $idCardBack ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$idCardBack) {
                echo $form->field($model, 'id_card_back')->widget(FileInput::class, [
                    'defaultFile' => $model->getFileUrl('id_card_back')
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));;
            } else {
                echo Html::tag(
                    'div',
                    $idCardBack ? Html::tag('img', options: ['src' => $idCardBack, 'alt' => 'id_card_back', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div id="military" class="col-12 col-md-4 <?= $model->sex == Module::getInstance()->user::SEX_WOMAN ? 'hide' : '' ?>">
            <?php
            $militaryDes = !!$model->military_description || $model->hasPendingData('military_description');
            $militaryDoc = $model->getFileUrl('military_doc');
            ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('military_doc') ?></label>
                <?php if ($militaryDoc): ?>
                    <div class="document-toolbar">
                        <a href="<?= $militaryDoc ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $militaryDoc ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                    <?php $galleryIndex++; endif; ?>
            </div>
            <div id="military-description" class="<?= !$militaryDes ? 'hide' : '' ?>">
                <?= $form->field($model, 'military_description')->textarea([
                    'rows' => 8,
                    'placeholder' => implode(' ', [
                        Module::t('module', 'Description'),
                        Module::t('module', 'Related To'),
                        $model->getAttributeLabel('military_doc')
                    ]),
                    'disabled' => !($model->isConfirmed || !$militaryDoc || $isAdmin),
                    'value' => $model->getAttributeValue('military_description', $isAdmin)
                ])
                ->label(false)
                ->hint(...$model->getPendingDataHint('military_description', $isAdmin)) ?>
            </div>
            <div id="military-doc" class="<?= $militaryDes ? 'hide' : '' ?>">
                <?php if (!$model->isConfirmed || $isAdmin || !$militaryDoc) {
                    echo $form->field($model, 'military_doc')->widget(FileInput::class, [
                        'defaultFile' => $model->getFileUrl('military_doc')
                    ])
                    ->label(false);
                } else {
                    echo Html::tag(
                        'div',
                        $militaryDoc ? Html::tag('img', options: ['src' => $militaryDoc, 'alt' => 'military_doc', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                        ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                    );
                } ?>
            </div>
            <label class="d-flex align-items-center justify-content-start gap-2" style="user-select: none">
                <input
                    id="military-checkbox"
                    type="checkbox"
                    <?= $militaryDes ? 'checked' : '' ?>
                    <?= !$model->isConfirmed || $isAdmin || (!$militaryDoc && !$militaryDes) ? '' : 'disabled' ?>
                />
                <span><?= Module::t('module', 'Evidence') . ' ' . Module::t('module', 'Military Service') ?> <?= $isAdmin ? 'ندارد.' : 'ندارم.' ?></span>
            </label>
        </div>

        <div class="col-12 py-4">
            <?= $this->render('../../layouts/_divider', [
                'title' => Module::t('module', 'Other Documents')
            ]) ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $educationPicture = $model->getFileUrl('education_picture'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('education_picture') ?></label>
                <?php if ($educationPicture): ?>
                    <div class="document-toolbar">
                        <a href="<?= $educationPicture ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $educationPicture ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$educationPicture) {
                echo $form->field($model, 'education_picture')->widget(FileInput::class, [
                    'defaultFile' => $model->getFileUrl('education_picture')
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));;
            } else {
                echo Html::tag(
                    'div',
                    $educationPicture ? Html::tag('img', options: ['src' => $educationPicture, 'alt' => 'education_picture', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $insuranceHistory = $model->getFileUrl('insurance_history'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('insurance_history') ?></label>
                <?php if ($insuranceHistory): ?>
                    <div class="document-toolbar">
                        <a href="<?= $insuranceHistory ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <a href="<?= $insuranceHistory ?>" data-gallery="show" data-gallery-index="<?= $galleryIndex ?>" target="_blank" title="<?= Module::t('module', 'Show Image') ?>">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </div>
                <?php $galleryIndex++; endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$insuranceHistory) {
                echo $form->field($model, 'insurance_history')->widget(FileInput::class, [
                    'defaultFile' => $model->getFileUrl('insurance_history')
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));;
            } else {
                echo Html::tag(
                    'div',
                    $insuranceHistory ? Html::tag('img', options: ['src' => $insuranceHistory, 'alt' => 'insurance_history', 'class' => 'dropify-wrapper-preview-img']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>

        <div class="col-12 col-md-4">
            <?php $resumeFile = $model->getFileUrl('resume_file'); ?>
            <div class="d-flex align-items-center justify-content-between">
                <label><?= $model->getAttributeLabel('resume_file') ?></label>
                <?php if ($resumeFile): ?>
                    <div class="document-toolbar">
                        <a href="<?= $resumeFile ?>" download="download" target="_blank" title="<?= Module::t('module', 'Download') ?>">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                    </div>
                    <?php endif; ?>
            </div>
            <?php if (!$model->isConfirmed || $isAdmin || !$resumeFile) {
                echo $form->field($model, 'resume_file')->widget(FileInput::class, [
                    'defaultFile' => $model->getFileUrl('resume_file')
                ])->label(false)->hint(Module::t('module', 'Maximum File Size', ['size' => $maxFileSize]));
            } else {
                echo Html::tag(
                    'div',
                    $resumeFile ? Html::tag('i', options: ['class' => 'far fa-file-pdf fa-4x']) : Html::tag('span', Module::t('module', 'Without Value')),
                    ['class' => 'dropify-wrapper d-flex align-items-center justify-content-center', 'style' => 'cursor: default']
                );
            } ?>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
