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
                'id',
                'description',
                'amount_limit:currency',
                'count_limit',
                'experience_limit',
                'request_again_limit',
                [
                    'attribute' => 'married',
                    'value' => function (Comfort $model) {
                        return Yii::$app->helper::itemAlias('CheckboxIcon',$model->married);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'showcase',
                    'value' => function (Comfort $model) {
                        return Yii::$app->helper::itemAlias('CheckboxIcon',$model->showcase ?: false);
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
