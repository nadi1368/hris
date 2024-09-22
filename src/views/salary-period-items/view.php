<?php

use common\components\Helper;
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
                'cost_of_spouse:currency',
                'count_of_children',
                'cost_of_children:currency',
                [
                    'attribute' => 'rate_of_year',
                    'value' => number_format((float)$model->rate_of_year),
                    'label' => 'نرخ پایه (سنوات)',
                ],

                [
                    'attribute' => 'hours_of_overtime',
                    'value' => function ($model) {
                        return isset($model->detailAddition['hours_of_overtime']) ?
                            Helper::renderLabelHelp($model->hours_of_overtime . ' (' . number_format((float)$model->getHoursOfOvertimeCost()) . ')', implode("<br />", $model->detailAddition['hours_of_overtime'])) :
                            $model->hours_of_overtime;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'holiday_of_overtime',
                    'value' => function ($model) {
                        return isset($model->detailAddition['holiday_of_overtime']) ?
                            Helper::renderLabelHelp($model->holiday_of_overtime . ' (' . number_format((float)$model->getHolidayOfOvertimeCost()) . ')', implode("<br />", $model->detailAddition['holiday_of_overtime'])) :
                            $model->holiday_of_overtime;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'night_of_overtime',
                    'value' => function ($model) {
                        return isset($model->detailAddition['night_of_overtime']) ?
                            Helper::renderLabelHelp($model->night_of_overtime . ' (' . number_format((float)$model->getNightOfOvertimeCost()) . ')', implode("<br />", $model->detailAddition['night_of_overtime'])) :
                            $model->night_of_overtime;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'hoursOfLowTime',
                    'value' => function ($model) {
                        return isset($model->detailAddition['hoursOfLowTime']) ?
                            Helper::renderLabelHelp($model->hoursOfLowTime . ' (' . number_format((float)$model->getHoursOfLowTimeCost()) . ')', implode("<br />", $model->detailAddition['hoursOfLowTime'])) :
                            $model->hoursOfLowTime;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'commission',
                    'value' => function ($model) {
                        return isset($model->detailAddition['commission']) ?
                            Helper::renderLabelHelp(number_format((float)$model->commission), implode("<br />", $model->detailAddition['commission'])) :
                            number_format((float)$model->commission);
                    },
                    'format' => 'raw',
                ],
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
                'salary_decrease:currency',
                [
                    'attribute' => 'non_cash_commission',
                    'value' => function ($model) {
                        return isset($model->detailAddition['non_cash_commission']) ?
                            Helper::renderLabelHelp(number_format($model->non_cash_commission),implode("<br />", $model->detailAddition['non_cash_commission'])) :
                            number_format($model->non_cash_commission);
                    },
                    'format' => 'raw',
                ],
                'payment_salary:currency',
                [
                    'attribute' => 'final_payment',
                    'value' => number_format((int)$model->finalPayment),
                    'format' => 'raw',
                ],
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
                'description',
                'descriptionShowEmployee',
            ],
        ]) ?>
    </div>
</div>
