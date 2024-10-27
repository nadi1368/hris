<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\models\Comfort;

/* @var $this yii\web\View */
/* @var $model Comfort */
?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'description',
                'amount_limit:currency',
                'count_limit',
            ]
        ]);
        ?>
    </div>
</div>

