<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\models\EmployeeBranch;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\EmployeeBranch */

?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'definite_id_salary',
                    'value' => function (EmployeeBranch $model) {
                        return $model->getDefiniteSalaryTitle(true);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'account_id_salary',
                    'value' => function (EmployeeBranch $model) {
                        return $model->getAccountSalaryTitle(true);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'definite_id_insurance_owner',
                    'value' => function (EmployeeBranch $model) {
                        return $model->getDefiniteInsuranceTitle(true);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'account_id_insurance_owner',
                    'value' => function (EmployeeBranch $model) {
                        return $model->getAccountInsuranceTitle(true);
                    },
                    'format' => 'raw'
                ],
            ]
        ]);
        ?>
    </div>
</div>
