<?php

use common\models\Rbac;
use hesabro\hris\Module;
use sadi01\bidashboard\widgets\ReportModalWidget;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use \hesabro\hris\models\EmployeeRollCall;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\EmployeeRollCallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Employee Roll Calls');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-roll-call-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a('آپلود فایل حضور و غیاب روزانه', ['employee-roll-call/upload-csv-daily'], ['class' => 'btn btn-success']) ?>
                <?= Yii::$app->client->identity->canAccess(Rbac::TYPE_BUSINESS_INTELLIGENT) && Yii::$app->user->can('master') ?
                    ReportModalWidget::widget([
                        'queryParams' => array_key_exists('EmployeeRollCallSearch', Yii::$app->request->queryParams) ? Yii::$app->request->queryParams['EmployeeRollCallSearch'] : [],
                        'searchModel' => $searchModel,
                        'searchModelMethod' => 'searchBiModule',
                        'searchModelRunResultView' => '@hesabro/hris/views/employee-roll-call/index.php',
                        'searchRoute' => Yii::$app->request->pathInfo,
                        'searchModelFormName' => $searchModel->formName(),
                        'outputColumn' => [
                            'total_over_time' => Module::t('module', 'Total') . ' ' . $searchModel->getAttributeLabel('over_time'),
                            'total_low_time' => Module::t('module', 'Total') . ' ' . $searchModel->getAttributeLabel('low_time'),
                            'total_mission_time' => Module::t('module', 'Total') . ' ' . $searchModel->getAttributeLabel('mission_time'),
                            'total_leave_time' => Module::t('module', 'Total') . ' ' . $searchModel->getAttributeLabel('leave_time'),
                        ],
                    ]) : null ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
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

                'id',
                [
                    'attribute' => 'user_id',
                    'value' => function (EmployeeRollCall $model) {
                        return $model->user->linkEmployee;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'date',
                    'value' => function (EmployeeRollCall $model) {
                        $time=strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($model->date));
                        return Yii::$app->jdf::jdate("l d F Y",$time);
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
                't_id',
            ],
        ]); ?>
    </div>
</div>
