<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\models\ComfortItems;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ComfortItems */

?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'description',
                'reject_description',
            ]
        ]);
        ?>
    </div>
    <div class="card-body">
        <?= Html::img($model->getCdnPhotoUrl('attach'), ['class' => 'img-responsive', 'width' => '200px']); ?>
    </div>
</div>
