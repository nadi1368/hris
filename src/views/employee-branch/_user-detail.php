<?php

use hesabro\helpers\widgets\TableView;

/* @var yii\web\View $this */
/* @var object $model */
?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'first_name',
                'last_name',
                'username',
            ]
        ])
        ?>
    </div>
</div>
