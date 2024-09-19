<?php
use hesabro\hris\models\EmployeeRequest;
use common\widgets\TableView;

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
