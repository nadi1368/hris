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
                [
                    'attribute' => 'loan_installment',
                    'value' => function (ComfortItems $model) {
                        return $model->loan_installment ? ComfortItems::itemAlias('Installments', $model->loan_installment) : null;
                    },
                    'format' => 'raw'
                ],
            ]
        ]);
        ?>
    </div>
    <div class="card-body">
        <?php
            $img = Html::img($model->getFileUrl('attach'), ['class' => 'img-responsive', 'width' => '200px']);
            echo Html::a($img, ['view-attach', 'id' => $model->id], ['class' => 'showModalButton']);
        ?>
    </div>
</div>
