<?php

use kartik\file\FileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use backend\modules\excel\models\UploadFormExcel;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model UploadFormExcel */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */

$this->title = Yii::t("app", "Excel File Upload");

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Salary Periods'), 'url' => ['salary-period/index']];
$this->params['breadcrumbs'][] = ['label' => $salaryPeriod->title, 'url' => ['salary-period-items/index', 'id' => $salaryPeriod->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'excelFile')->fileInput() ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Yii::t("app", "Upload"), ['class' => 'btn btn-success btn btn-flat']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>


