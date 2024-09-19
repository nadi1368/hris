<?php

use common\widgets\TableView;
use hesabro\hris\models\Comfort;
use common\components\Helper;

/* @var $this yii\web\View */
/* @var $model Comfort */

?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'description',
                'amount_limit:currency',
                'count_limit',
                'experience_limit',
                'request_again_limit',
                [
                    'attribute' => 'married',
                    'value' => function (Comfort $model) {
                        return Helper::itemAlias('CheckboxIcon',$model->married);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'showcase',
                    'value' => function (Comfort $model) {
                        return Helper::itemAlias('CheckboxIcon',$model->showcase ?: false);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'users',
                    'value' => function (Comfort $model) {
                        return $model->usersList;
                    },
                    'format' => 'raw'
                ],
            ]
        ]);
        ?>
    </div>
</div>
