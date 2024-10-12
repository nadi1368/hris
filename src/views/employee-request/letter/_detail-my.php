<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\models\EmployeeRequest;

/**
 * @var EmployeeRequest $model
 */
?>

<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'description',
                'reject_description'
            ]
        ]);
        ?>
    </div>
</div>
