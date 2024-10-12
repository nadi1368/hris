<?php

use kartik\file\FileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use common\models\UploadExcel;
use hesabro\hris\Module;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $uploadForm UploadExcel */

$this->title = 'آپلود فایل حضور و غیاب روزانه';

$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Periods'), 'url' => ['salary-period/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($uploadForm, 'date')->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                ]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($uploadForm, "excelFile")->fileInput() ?>
            </div>
        </div>


    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', "Upload"), ['class' => 'btn btn-success btn btn-flat']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>


