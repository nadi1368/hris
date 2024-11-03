<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\Module;

/**
 * @var EmployeeRequest $model
 */
?>

<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'contract_template_id',
                    'value' => function (EmployeeRequest $model) {
                        return $model->contractTemplate?->title;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'au_letter_id',
                    'label' => Module::t('module', 'Document Number'),
                    'value' => function (EmployeeRequest $model) {
                        return $model->auLetter?->number ?: '-';
                    },
                    'format' => 'raw'
                ],
                'description',
                'reject_description'
            ]
        ]);
        ?>
    </div>
</div>
