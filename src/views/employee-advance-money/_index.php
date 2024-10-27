<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\models\AdvanceMoney;

/* @var $this yii\web\View */
/* @var $model AdvanceMoney */

?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'comment',
                'reject_comment',
            ]
        ]);
        ?>
    </div>
</div>
