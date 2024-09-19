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
                'id',
                [
                    'attribute' => 'contract_template_id',
                    'value' => function (EmployeeRequest $model) {
                        return $model->contractTemplate?->title;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'indicator_id',
                    'label' => Yii::t('app', 'Document Number') . ' ' .Yii::t('app', 'Indicator'),
                    'value' => function (EmployeeRequest $model) {
                        return $model->indicator?->document_number;
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
