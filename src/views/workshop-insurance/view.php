<?php

use hesabro\helpers\widgets\TableView;
use hesabro\hris\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\WorkshopInsurance */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Workshop Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workshop-insurance-view card">
	<div class="card-body">
	<?= TableView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'code',
            'title',
            'manager',
            [
                'attribute' => 'created',
                'value' => function ($model) {
                    return '<span title="بروز رسانی شده در ' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created) . '</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'creator_id',
                'value' => function ($model) {
                    return '<span title="بروز رسانی شده توسط ' . $model->update->fullName . '">' . $model->creator->fullName . '</span>';
                },
                'format' => 'raw'
            ],
		],
	]) ?>
    <?= TableView::widget([
        'model' => $model,
        'attributes' => [
            'address',
            'row',
        ],
    ]) ?>
	</div>
</div>
