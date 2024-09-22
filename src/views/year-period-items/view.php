<?php

use yii\helpers\Html;
use common\widgets\TableView;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\SalaryPeriodItems */

?>
<div class="salary-period-items-view card">
	<div class="card-body">
	<?= TableView::widget([
		'model' => $model,
		'attributes' => [
//            'id',
//            'period_id',
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model->user->linkEmployee;
                },
                'format' => 'raw',
            ],
            'hours_of_work',
            'treatment_day',
            'holiday_of_overtime',
            'night_of_overtime',
            'basic_salary:currency',
            'cost_of_house:currency',
            'cost_of_food:currency',
            'count_of_children',
            'cost_of_children:currency',
            [
                'attribute' => 'rate_of_year',
                'value'=> number_format((float)$model->rate_of_year),
                'label' => 'نرخ پایه (سنوات)',
            ],
            'hours_of_overtime',
            'rate_of_overtime',
            'commission:currency',
            'insurance:currency',
            'insurance_owner:currency',
            'tax:currency',
		],
	]) ?>
	<?= TableView::widget([
		'model' => $model,
		'attributes' => [
            'count_point',
            'cost_point:currency',
            'cost_of_trust:currency',
            'total_salary:currency',
            'advance_money:currency',
            'payment_salary:currency',
            [
                'attribute' => 'created',
                'value' => function ($model) {
                    return '<span title="بروز رسانی شده در '.Yii::$app->jdf->jdate("Y/m/d  H:i", $model->changed).'">'.Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created).'</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'creator_id',
                'value' => function ($model) {
                    return '<span title="بروز رسانی شده توسط '.$model->update->fullName.'">'.$model->creator->fullName.'</span>';
                },
                'format' => 'raw'
            ],
		],
	]) ?>

	</div>
</div>
