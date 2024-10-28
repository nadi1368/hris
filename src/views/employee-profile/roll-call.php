<?php

use hesabro\hris\models\EmployeeRollCall;
use hesabro\helpers\components\Jdf;
use common\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\EmployeeRollCallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'حضور و غیاب';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="employee-roll-call-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="true">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>

            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse show" aria-expanded="true">
            <?= $this->render('_search-roll-call', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}\n<div class='table-responsive mb-2  text-center'>{items}</div>{pager}",
			'showFooter' => true,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['class' => $model->getStatusCssClass()];
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'date',
                    'value' => function (EmployeeRollCall $model) {
                        $time=strtotime(Jdf::Convert_jalali_to_gregorian($model->date));
                        return Yii::$app->jdate->date("l d F Y",$time);
                    },
                    'format' => 'raw',
                ],
                'status',
                'total',
                'shift',
                [
					'attribute' => 'over_time',
					'value' => function (EmployeeRollCall $model) {
						return $model->over_time;
					},
					'footer' => $dataProvider->query->sum('over_time'),
				],
                [
					'attribute' => 'low_time',
					'value' => function (EmployeeRollCall $model) {
						return $model->low_time;
					},
					'footer' => $dataProvider->query->sum('low_time'),
				],
                [
					'attribute' => 'mission_time',
					'value' => function (EmployeeRollCall $model) {
						return $model->mission_time;
					},
					'footer' => $dataProvider->query->sum('mission_time'),
				],
                [
					'attribute' => 'leave_time',
					'value' => function (EmployeeRollCall $model) {
						return $model->leave_time;
					},
					'footer' => $dataProvider->query->sum('leave_time'),
				],
                'in_1',
                'out_1',
                'in_2',
                'out_2',
                'in_3',
                'out_3',
            ],
        ]); ?>
    </div>
</div>
