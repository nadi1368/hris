<?php

use hesabro\helpers\models\UploadExcel;
use kartik\file\FileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $uploadForm UploadExcel */

$this->title = 'آپلود فایل مزایای غیر نقدی';

$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Items Additions'), 'url' => ['salary-items-addition/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($uploadForm, 'month')->dropdownList(UploadExcel::itemAlias('Months')) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($uploadForm, "file_name")->fileInput() ?>
            </div>
        </div>


    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', "Upload"), ['class' => 'btn btn-success btn btn-flat']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>


