<?php

use hesabro\hris\models\RequestLeave;
use hesabro\helpers\widgets\TableView;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model backend\models\RequestLeave */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Request Leaves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-leave-view card">
    <div class="card-body">
        <?= TableView::widget([
            'model'      => $model,
            'attributes' => [
                [
                    'attribute' => 'user_id',
                    'value'     => function ($model) {
                        return $model->user->getLink();
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'type',
                    'value'     => function ($model) {
                        return RequestLeave::itemAlias('Types', $model->type);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value'     => function ($model) {
                        return RequestLeave::itemAlias('Status', $model->status);
                    },
                    'format'    => 'raw'
                ],
                'description:ntext',
                [
                    'attribute' => 'from_date',
                    'value'     => function ($model) {
                        return RequestLeave::itemAlias('TypesDaily') ? Yii::$app->jdate->date("Y/m/d", $model->from_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->from_date);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'to_date',
                    'value'     => function ($model) {
                        return RequestLeave::itemAlias('TypesDaily') ? Yii::$app->jdate->date("Y/m/d", $model->to_date) : Yii::$app->jdate->date("Y/m/d  H:i", $model->to_date);
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'created',
                    'value'     => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format'    => 'raw'
                ],
                [
                    'attribute' => 'creator_id',
                    'value'     => function ($model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update->fullName . '">' . $model->creator->fullName . '</span>';
                    },
                    'format'    => 'raw'
                ],
            ],
        ]) ?>
    </div>
</div>
