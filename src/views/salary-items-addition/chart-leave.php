<?php

use hesabro\hris\bundles\ChartLeave;
use hesabro\hris\Module;
use yii\web\View;

/* @var $this View */
/* @var $searchModel hesabro\hris\models\SalaryItemsAdditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var array $chartData */

ChartLeave::register($this);

$this->title = 'نمودار مرخصی کارمندان';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0"><?= Module::t('module', 'Employee Leave Chart') ?></h4>
    </div>
    <div class="card-body">
        <div id="report-leave-chart" style="width: 100%; height: 750px"></div>
    </div>
</div>

<?php
$chartData = json_encode($chartData);
$day = Module::t('module', 'Day');
$hour = Module::t('module', 'Hour');
$js = <<<JS
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    const data = google.visualization.arrayToDataTable($chartData) 
    const chart = new google.visualization.LineChart(document.getElementById('report-leave-chart'));
    chart.draw(data, {
        fontName: 'IRANSans',
        legend: {
            position: 'bottom' 
        },
        vAxis: {
            ticks: [
                {v:0, f:''}, {v:0.5, f:'4 $hour'},
                {v:1, f:'1 $day'}, {v:1.5, f:'1/5 $day'},
                {v:2, f:'2 $day'}, {v:2.5, f:'2/5 $day'},
                {v:3, f:'3 $day'}, {v:3.5, f:'3/5 $day'},
                {v:4, f:'4 $day'}
            ]
        }
    });
}
JS;

$this->registerJs($js, View::POS_END);
?>
