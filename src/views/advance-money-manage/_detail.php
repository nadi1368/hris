<?php

use common\widgets\TableView;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use backend\models\AdvanceMoney;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\Process;

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
