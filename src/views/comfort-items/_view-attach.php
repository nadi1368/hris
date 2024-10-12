<?php

use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\ComfortItems */

?>
<div class="card-header">
    <?= Html::a(Module::t('module', 'Download'), $model->getCdnPhotoUrl('attach'), ['target' => '_blank', 'class' => 'btn btn-info']) ?>
</div>
<div class="col-md-12">
    <?= Html::img($model->getCdnPhotoUrl('attach'), ['class' => 'img-responsive w-100']); ?>
</div>