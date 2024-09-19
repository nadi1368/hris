<?php

use hesabro\hris\models\OrganizationMember;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\OrganizationMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'چارت سازمانی';
$this->params['breadcrumbs'][] = $this->title;

$data = [];
foreach ($dataProvider->getModels() as $model) {
    $data[] = [
        'id' => $model->id,
        'name' => $model->name,
        'title' => $model->getFullHeadline() ?: $model->user->job,
        'image' => $model->user?->getFileUrl('avatar') ?: 'https://cdn.balkan.app/shared/empty-img-white.svg'
    ];
}

$highchartsHierarchy = OrganizationMember::buildHighchartsHierarchy($dataProvider->getModels());
$highchartsNodes = OrganizationMember::buildHighchartsData($highchartsHierarchy); ?>

<div dir="ltr" class="p-4 bg-white">
    <?= Highcharts::widget([
        'scripts' => [
            'modules/sankey',
            'modules/organization',
            'modules/exporting',
            'modules/accessibility',
            'modules/grid',
        ],
        'options' => [
            "chart" => [
                "type" => "organization",
                "height" => (count($data) + 1) * 90,
                "inverted" => true,
                "style" => [
                    "fontFamily" => "inherit"
                ]
            ],
            "title" => [
                "text" => "چارت سازمانی"
            ],
            "series" => [[
                "type" => "organization",
                "name" => "چارت سازمانی",
                "keys" => ["from", "to"],
                "data" => $highchartsNodes,
                "nodes" => $data,
                "colorByPoint" => false,
                "color" => "#02abc0",
                "dataLabels" => [
                    "color" => "white"
                ],
                "borderColor" => "white",
                "borderRadius" => "7%",
                "nodeWidth" => 100,
                "borderWidth" => 1,
            ]],
            "tooltip" => [
                "outside" => true,
            ],
            "exporting" => [
                "allowHTML" => true,
                "sourceWidth" => 800,
                "sourceHeight" => (count($data) + 1) * 110
            ]
        ],
    ]); ?>
</div>