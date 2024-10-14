<?php

use hesabro\hris\models\Comfort;
use hesabro\hris\models\Content;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\SalaryItemsAddition;
use hesabro\helpers\components\iconify\Iconify;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;
use common\models\Comments;

/**
 * @var yii\web\View $this
 * @var object $user
 * @var int|null $workTime
 * @var int|null $overTimeDay
 * @var int|null $overTimeNight
 * @var int|null $overTimeHoliday
 * @var int|null $lowTime
 * @var EmployeeBranchUser $employee
 * @var float $monthLeaveLimit
 * @var float $monthLeaveRemain
 * @var float $monthLeaveTotal
 * @var float $yearLeaveLimit
 * @var float $yearLeaveRemain
 * @var float $yearLeaveTotal
 * @var Comfort[] $comforts
 * @var object[] $lastRequests
 * @var string $monthText
 * @var string $yearText
 * @var int $monthValue
 * @var Content[] $banners
 */

$this->title = Yii::t('app', 'Home');
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin([
    'id' => 'profile',
    'enablePushState' => false,
    'enableReplaceState' => false,
    'linkSelector' => false
]);

$yearLeaveLimitText = Yii::$app->formatter->asDuration(Yii::$app->helper::numberToWorkTime($yearLeaveLimit)['duration'], '  و ');
$monthLeaveLimitText = Yii::$app->formatter->asDuration(Yii::$app->helper::numberToWorkTime($monthLeaveLimit)['duration'], '  و ');
$yearLeaveText = $yearLeaveTotal > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($yearLeaveTotal)['duration'], '  و ') : Yii::t('app', 'Without Leave');
$monthLeaveText = $monthLeaveTotal > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($monthLeaveTotal)['duration'], '  و ') : Yii::t('app', 'Without Leave');
$yearLeaveRemainText = $yearLeaveRemain > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($yearLeaveRemain)['duration'], '  و ') : Yii::t('app', 'Without Leave');
$monthLeaveRemainText = $monthLeaveRemain > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($monthLeaveRemain)['duration'], '  و ') : Yii::t('app', 'Without Leave');
$overTimeDayText = $overTimeDay > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($overTimeDay)['duration'], '  و ') : Yii::t('app', 'Without Overtime');
$overTimeNightText = $overTimeNight > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($overTimeNight)['duration'], '  و ') : Yii::t('app', 'Without Night Work');
$overTimeHolidayText = $overTimeHoliday > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($overTimeHoliday)['duration'], '  و ') : Yii::t('app', 'Without Holiday Work');
$lowTimeText = $lowTime > 0 ? Yii::$app->formatter->asDuration((int) Yii::$app->helper::numberToWorkTime($lowTime)['duration'], '  و ') : Yii::t('app', 'Without Low Time');
$workTimeText = $workTime ? $workTime . ' ' . Yii::t('app', 'Day') : Yii::t('app', 'Without Worktime');
?>

<?php if (count($banners)): ?>
    <div id="employeeFaqBanner" class="carousel slide mb-3" data-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($banners as $bannerIndex => $banner): ?>
                <div class="carousel-item <?= $bannerIndex === 0 ? 'active' : '' ?>">
                    <div class="alert alert-info mb-0" role="alert">
                        <h4 class="alert-heading"><?= $banner->title ?></h4>
                        <p class="mb-0"><?= $banner->description ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($banners) > 1): ?>
            <ol class="carousel-indicators mb-0">
                <?php foreach ($banners as $bannerIndex => $banner): ?>
                    <li data-target="#carouselExampleIndicators" data-slide-to="<?= $bannerIndex ?>" class="<?= $bannerIndex === 0 ? 'active' : '' ?>"></li>
                <?php endforeach; ?>
            </ol>
            <button class="carousel-control-prev faq-announcement-carousel-prev" type="button" data-target="#employeeFaqBanner" data-slide="prev">
                <?= Iconify::getInstance()->icon('ph:arrow-circle-left') ?>
                <span class="sr-only">Previous</span>
            </button>
            <button class="carousel-control-next faq-announcement-carousel-next" type="button" data-target="#employeeFaqBanner" data-slide="next">
                <?= Iconify::getInstance()->icon('ph:arrow-circle-right') ?>
                <span class="sr-only">Next</span>
            </button>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="profile-grid gap-4" style="position: relative">
    <div id="loading-overlay" class="profile-overlay" style="display: none">
        <?= Iconify::getInstance()->icon('svg-spinners:pulse-rings-3') ?>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center justify-content-start" style="gap: 16px">
            <?php if ($avatar = $user->getFileUrl('avatar')): ?>
                <img src="<?= $avatar ?>" alt="avatar" width="75" height="75" class="rounded-circle"
                     style="object-fit: cover; object-position: center;"/>
            <?php else: ?>
                <i class="fal fa-user-circle fa-6x pt-3 pull-right"></i>
            <?php endif; ?>
            <div class="d-flex flex-column align-items-start justify-content-center" style="flex: 1; gap: 12px">
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><?= $user->fullName ?> به <span data-toggle="tooltip"
                                                                     data-title="<?= Yii::t('app', 'HRIS') ?>">Pulse</span> <?= Yii::t('app', 'Welcome') ?>
                    </h4>
                </div>

                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <p class="d-inline-block mb-0"><?= $employee?->salaryInsurance?->group ?: '-' ?></p>
                        <?php
                        $code = $employee?->salaryInsurance?->code;
                        $description = $employee?->description_work;
                        ?>
                        <?php if ($description || $code): ?>
                            <p class="d-inline-block mb-0 text-muted"><small>(<?= $code ?> - <?= $description ?>)</small></p>
                        <?php endif; ?>
                    </div>
                    <a href="<?= Url::to(['/employee/profile/update']) ?>" class="d-block text-success font-24">
                        <?= Iconify::getInstance()->icon('ph:note-pencil-duotone') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-grid-cards gap-4">
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #dbf0e3; color: #1b4a38;">
                        <?= Iconify::getInstance()->icon('ph:clock-clockwise-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $overTimeDayText ?>"><?= $overTimeDayText ?></p>
                        <p class="mb-0 text-muted profile-card-subtitle"><?= Yii::t('app', 'Overtime') ?> <?= Yii::t('app', 'Daily') ?>
                            <strong><?= $monthText ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #fee2e2; color: #b71e1e">
                        <?= Iconify::getInstance()->icon('ph:clock-counter-clockwise-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $lowTimeText ?>"><?= $lowTimeText ?></p>
                        <p class="mb-0 text-muted profile-card-subtitle"><?= SalaryItemsAddition::itemAlias('Kind', SalaryItemsAddition::KIND_LOW_TIME) ?>
                            <strong><?= $monthText ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #e4e7e9; color: #474d57">
                        <?= Iconify::getInstance()->icon('ph:moon-stars-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $overTimeNightText ?>"><?= $overTimeNightText ?></p>
                        <p class="mb-0 text-muted profile-card-subtitle"><?= SalaryItemsAddition::itemAlias('Type', SalaryItemsAddition::TYPE_OVER_TIME_NIGHT) ?>
                            <strong><?= $monthText ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #ffefd3; color: #cc4802;">
                        <?= Iconify::getInstance()->icon('ph:calendar-check-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $overTimeHolidayText ?>"><?= $overTimeHolidayText ?></p>
                        <p class="mb-0 text-muted profile-card-subtitle"><?= SalaryItemsAddition::itemAlias('Type', SalaryItemsAddition::TYPE_OVER_TIME_HOLIDAY) ?>
                            <strong><?= $monthText ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-grid-cards gap-4">
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #e1e1fe; color: #4f33a0;">
                        <?= Iconify::getInstance()->icon('ph:briefcase-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $workTimeText ?>"><?= $workTimeText ?></p>
                        <p class="mb-0 text-muted profile-card-subtitle"><?= Yii::t('app', 'Cooperation Time') ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #e0e7e4; color: #475a56;">
                        <svg style="animation: beat .75s infinite alternate;" xmlns="http://www.w3.org/2000/svg"
                             width="36" height="36" viewBox="0 0 48 48">
                            <path fill="#45413c" d="M10.41 44.23a13.59 1.77 0 1 0 27.18 0a13.59 1.77 0 1 0-27.18 0"
                                  opacity=".15"/>
                            <path fill="#ff6242"
                                  d="M33.15 3.25h-2.74c-1.82 0-3.26 1.15-3.15 2.51l1.62 20.74c.09 1.15 1.36 2.05 2.9 2.05s2.81-.9 2.9-2.05L36.3 5.76c.1-1.36-1.3-2.51-3.15-2.51"/>
                            <path fill="#ff866e"
                                  d="M27.44 8.11a3.26 3.26 0 0 1 3-1.56h2.74a3.24 3.24 0 0 1 3 1.56l.19-2.35C36.4 4.4 35 3.25 33.15 3.25h-2.74c-1.82 0-3.26 1.15-3.15 2.51Z"/>
                            <path fill="none" stroke="#45413c" stroke-linecap="round" stroke-linejoin="round"
                                  d="M33.15 3.25h-2.74c-1.82 0-3.26 1.15-3.15 2.51l1.62 20.74c.09 1.15 1.36 2.05 2.9 2.05s2.81-.9 2.9-2.05L36.3 5.76c.1-1.36-1.3-2.51-3.15-2.51"/>
                            <path fill="#ff6242" d="M28.22 35.2a3.56 3.56 0 1 0 7.12 0a3.56 3.56 0 1 0-7.12 0"/>
                            <path fill="#ff866e"
                                  d="M31.78 33.93a3.49 3.49 0 0 1 3.48 2a3.67 3.67 0 0 0 .08-.75a3.57 3.57 0 0 0-7.13 0a3.08 3.08 0 0 0 .09.75c.34-1.14 1.77-2 3.48-2"/>
                            <path fill="none" stroke="#45413c" stroke-linecap="round" stroke-linejoin="round"
                                  d="M28.22 35.2a3.56 3.56 0 1 0 7.12 0a3.56 3.56 0 1 0-7.12 0"/>
                            <path fill="#ff6242"
                                  d="M17.59 3.25h-2.74C13 3.25 11.6 4.4 11.7 5.76l1.62 20.74c.09 1.15 1.37 2.05 2.9 2.05s2.81-.9 2.9-2.05l1.62-20.74c.11-1.36-1.33-2.51-3.15-2.51"/>
                            <path fill="#ff866e"
                                  d="M11.89 8.11a3.24 3.24 0 0 1 3-1.56h2.74a3.26 3.26 0 0 1 3 1.56l.18-2.35c.11-1.36-1.33-2.51-3.15-2.51h-2.81C13 3.25 11.6 4.4 11.7 5.76Z"/>
                            <path fill="none" stroke="#45413c" stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.59 3.25h-2.74C13 3.25 11.6 4.4 11.7 5.76l1.62 20.74c.09 1.15 1.37 2.05 2.9 2.05s2.81-.9 2.9-2.05l1.62-20.74c.11-1.36-1.33-2.51-3.15-2.51"/>
                            <path fill="#ff6242" d="M12.66 35.2a3.56 3.56 0 1 0 7.12 0a3.56 3.56 0 1 0-7.12 0"/>
                            <path fill="#ff866e"
                                  d="M16.22 33.93c1.71 0 3.14.86 3.48 2a3.08 3.08 0 0 0 .09-.75a3.57 3.57 0 0 0-7.13 0a3.67 3.67 0 0 0 .08.75a3.49 3.49 0 0 1 3.48-2"/>
                            <path fill="none" stroke="#45413c" stroke-linecap="round" stroke-linejoin="round"
                                  d="M12.66 35.2a3.56 3.56 0 1 0 7.12 0a3.56 3.56 0 1 0-7.12 0"/>
                        </svg>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0">نمی‌دونیم چی بذاریم اینجا</p>
                        <p class="mb-0 text-muted profile-card-subtitle">شما <a class="text-info showModalButton"
                                                                                title="تیکت پشتیبانی"
                                                                                href="<?= Url::toRoute(['ticket/send', 'type' => Comments::TYPE_MASTER]) ?>"><u>پیشنهاد</u></a>
                            کنین چی باشه :)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #f4ecec; color: #9b6767">
                        <?= Iconify::getInstance()->icon('ph:power-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $monthLeaveText ?>"><?= $monthLeaveText ?></p>
                        <div class="w-100 d-flex align-items-center justify-content-between">
                            <p class="mb-0 text-muted profile-card-subtitle">
                                <?= Yii::t('app', 'Leave') ?>
                                <strong><?= $monthText ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-start">
                <div class="w-100 d-flex align-items-center justify-content-start gap-2">
                    <div class="profile-card-icon font-32" style="background-color: #e0eced; color: #3c5b62;">
                        <?= Iconify::getInstance()->icon('ph:power-duotone') ?>
                    </div>
                    <div class="w-100 d-flex flex-column align-items-start justify-content-start gap-1">
                        <p class="mb-0 profile-card-title" data-toggle="tooltip"
                           data-title="<?= $yearLeaveText ?>"><?= $yearLeaveText ?></p>
                        <p class="mb-0 text-muted profile-card-subtitle"><?= Yii::t('app', 'Total') ?> <?= Yii::t('app', 'Leave') ?>
                            <strong><?= $yearText ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-0" style="max-height: 650px;">
        <div class="card-header d-flex align-items-center gap-2">
            <?= Iconify::getInstance()->icon('ph:gift-duotone', 'font-20') ?>
            <span>امکانات رفاهی</span>
        </div>
        <div class="card-body" style="max-height: 100%; overflow-y: auto;">
            <?php if (count($comforts)): ?>
                <div class="h-100 d-flex flex-column justify-content-between">
                    <ul>
                        <?php $comfortsLastIndex = $lastIndex = (count($comforts) - 1); ?>
                        <?php foreach ($comforts as $index => $comfort): ?>
                            <li class="px-2 py-3"
                                style="<?= $index < $comfortsLastIndex ? 'border-bottom: 1px solid #d1d5db;' : '' ?>">
                                <a href="<?= Url::toRoute('comfort/index') ?>">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="profile-card-icon font-24"
                                                     style="width: 35px; height: 35px; background-color: <?= Yii::$app->helper::hexAdjustBrightness(Comfort::itemAlias('CatBg', $comfort->type)[1], 200) ?>; color: <?= Comfort::itemAlias('CatBg', $comfort->type)[1] ?>;">
                                                    <?= Iconify::getInstance()->icon('ph:gift-duotone') ?>
                                                </div>
                                                <span><?= $comfort->title ?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span><?= $comfort->amount_limit ? number_format((float)$comfort->amount_limit) . ' ' . Yii::t('app', 'Rial') : Yii::t('app', 'No Limit'); ?></span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center">
                    <?= Yii::t('app', 'Not items found.') ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (count($comforts)): ?>
            <div class="card-footer bg-white">
                <div class="d-flex align-items-center justify-content-end">
                    <?= Html::a('مشاهده بیشتر و درخواست', ['comfort/index'], [
                        'class' => 'btn btn-info'
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="card mb-0">
        <div class="card-header d-flex align-items-center gap-2">
            <?= Iconify::getInstance()->icon('ph:chart-donut-duotone', 'font-20') ?>
            <span>نمودار گزارش مرخصی</span>
        </div>
        <div class="card-body p-0">
            <?= Highcharts::widget([
                'options' => [
                    'caption' => [
                        'text' => 'برای مشاهده جزئیات ببشتر، موس را روی نمودار حرکت دهید',
                        'align' => 'center',
                        'margin' => 0,
                        'x' => 0,
                        'y' => -10
                    ],
                    'chart' => [
                        'type' => 'solidgauge',
                        'height' => '100%',
                        'style' => [
                            'fontFamily' => 'IRANSans'
                        ],
                        'events' => [
                            'render' => new JsExpression("function() { $('svg.highcharts-root > text.highcharts-credits').remove() }"),
                        ]
                    ],
                    'title' => [
                        'text' => new JsExpression('undefined')
                    ],
                    'tooltip' => [
                        'borderWidth' => 0,
                        'backgroundColor' => 'none',
                        'shadow' => false,
                        'style' => [
                            'fontSize' => '16px',
                            'textAlign' => 'center'
                        ],
                        'valueSuffix' => '٪',
                        'useHTML' => true,
                        'pointFormat' => '<span style="font-size: 2em; color: {point.color}; font-weight: bold">{point.y}</span>{series.name}',
                        'positioner' => new JsExpression('function (labelWidth) { return { x: (this.chart.chartWidth - labelWidth) / 2, y: (this.chart.plotHeight / 2) - 65 }}')
                    ],
                    'pane' => [
                        'startAngle' => 0,
                        'endAngle' => 360,
                        'background' => [
                            [
                                'outerRadius' => '87%',
                                'innerRadius' => '63%',
                                'backgroundColor' => '#d0f7ea',
                                'borderWidth' => 0
                            ],
                            [
                                'outerRadius' => '62%',
                                'innerRadius' => '38%',
                                'backgroundColor' => '#dfebee',
                                'borderWidth' => 0
                            ]
                        ]
                    ],
                    'yAxis' => [
                        'min' => 0,
                        'max' => 100,
                        'lineWidth' => 0,
                        'tickPositions' => [
                        ]
                    ],
                    'plotOptions' => [
                        'solidgauge' => [
                            'dataLabels' => [
                                'enabled' => false
                            ],
                            'linecap' => 'round',
                            'stickyTracking' => false,
                            'rounded' => true
                        ]
                    ],
                    'series' => [
                        [
                            'name' => '<br/><small class="text-muted plain-text">' . $yearLeaveRemainText . '</small><br/><hr class="m-0" /><span style="font-size: 14px">' . Yii::t('app', 'Leave') . ' ' . Yii::t('app', 'Remain') . '<br>' . $yearText . '</span>',
                            'data' => [
                                [
                                    'color' => '#36ba98',
                                    'radius' => '87%',
                                    'innerRadius' => '63%',
                                    'y' => 100 - ceil(($yearLeaveTotal * 100) / $yearLeaveLimit)
                                ]
                            ]
                        ],
                        [
                            'name' => '<br/><small class="text-muted plain-text">' . $monthLeaveRemainText . '</small><br/><hr class="m-0" /><span style="font-size: 14px">' . Yii::t('app', 'Leave') . ' ' . Yii::t('app', 'Remain') . '<br>' . $monthText . '</span>',
                            'data' => [
                                [
                                    'color' => '#6295a2',
                                    'radius' => '62%',
                                    'innerRadius' => '38%',
                                    'y' => 100 - ceil(($monthLeaveTotal * 100) / $monthLeaveLimit)
                                ]
                            ]
                        ]
                    ]
                ],
                'scripts' => [
                    'highcharts-more',
                    'modules/solid-gauge',
                    'modules/accessibility'
                ]
            ]) ?>
        </div>
    </div>
    <div class="card mb-0" style="max-height: 650px;">
        <div class="card-header d-flex align-items-center gap-2">
            <?= Iconify::getInstance()->icon('ph:newspaper-clipping-duotone', 'font-20') ?>
            <span>آخرین درخواست‌ها</span>
        </div>
        <div class="card-body" style="max-height: 100%; overflow-y: auto;">
            <?php if (count($lastRequests)): ?>
                <table class="table kv-grid-table">
                    <thead>
                    <tr>
                        <th><?= Yii::t('app', 'Title') ?></th>
                        <th><?= Yii::t('app', 'Date') ?></th>
                    </tr>
                    </thead>
                    <?php foreach ($lastRequests as $lastRequest): ?>
                        <tbody>
                        <tr>
                            <td class="d-flex align-items-center justify-content-start gap-1">
                                <span class="<?= isset($lastRequest['color']) ? "text-{$lastRequest['color']} font-20" : '' ?>"
                                      data-toggle="tooltip" data-title="<?= $lastRequest['status'] ?? '' ?>">
                                    <?= Iconify::getInstance()->icon($lastRequest['icon'] ?? '') ?>
                                </span>
                                <span><?= $lastRequest['title'] ?></span>
                            </td>
                            <td style="width: 20%"><?= Yii::$app->jdate->date('Y/m/d', $lastRequest['date'] ?? 0) ?></td>
                        </tr>
                        </tbody>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center">
                    <?= Yii::t('app', 'Not items found.') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
Pjax::end();

$url = Url::to(['profile/index']);
$yearOptions = '';
$monthOptions = '';
for ($year = 1403; $year >= 1400; $year--) {
    $yearOptions .= Html::tag('a', $year, [
        'class' => 'dropdown-item',
        'href' => 'javascript:void(0)',
        'data' => [
            'value' => $year,
            'type' => 'year',
            'selected' => $year === ((int)$yearText)
        ]
    ]);
}

for ($month = 1; $month <= 12; $month++) {
    $monthOptions .= Html::tag('a', Yii::$app->jdf::jdate('F', Yii::$app->jdf::jalaliToTimestamp(Yii::$app->jdf::jdate("Y/$month/01"), 'Y/m/d')), [
        'class' => 'dropdown-item',
        'href' => 'javascript:void(0)',
        'data' => [
            'value' => $month,
            'type' => 'month',
            'selected' => $month === $monthValue
        ]
    ]);
}

$requestLeaveHourlyUrl = Url::to(['/request-leave/create']);
$requestLeaveHourlyTitle = Yii::t('app', 'Request Leave Hourly');

$requestLeaveDailyUrl = Url::to(['/request-leave/create-daily']);
$requestLeaveDailyTitle = Yii::t('app', 'Request Leave Daily');

$navbar = <<<HTML
<li class="nav-item btn-group dropdown">
    <a class="nav-link dropdown-toggle overflow-hidden font-bold" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)">
        <span>$yearText</span>
        <span class="fal fa-angle-down ml-2 user-text"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-left dropdown-menu-sm-right animated flipInY">
        $yearOptions
    </div>
</li>
<li class="nav-item btn-group dropdown">
    <a class="nav-link dropdown-toggle overflow-hidden font-bold" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)">
        <span>$monthText</span>
        <span class="fal fa-angle-down ml-2 user-text"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-left dropdown-menu-sm-right animated flipInY">
        $monthOptions
    </div>
</li>
<li class="nav-item btn-group">
    <a
        href="javascript:void(0)"
        class="nav-link"
        data-toggle="modal"
        data-target="#modal-pjax"
        data-size="modal-lg"
        data-reload-pjax-container="profile"
        data-url="$requestLeaveHourlyUrl"
        data-title="$requestLeaveHourlyTitle"
    >$requestLeaveHourlyTitle</a>
</li>
<li class="nav-item btn-group">
    <a
        href="javascript:void(0)"
        class="nav-link"
        data-toggle="modal"
        data-target="#modal-pjax"
        data-size="modal-lg"
        data-reload-pjax-container="profile"
        data-url="$requestLeaveDailyUrl"
        data-title="$requestLeaveDailyTitle"
    >$requestLeaveDailyTitle</a>
</li>
HTML;

$js = <<<JS
const navActions = $('#navbar-actions')
navActions.append(`$navbar`)
function changeDateRange() {
    const year = $("a[data-type='year'][data-selected]")?.data('value')
    const month = $("a[data-type='month'][data-selected]")?.data('value')
    $.pjax.reload('#profile', {
        url: `$url?year=\${year}&month=\${month}`,
        replace: false
    })
}

$('#profile').on('pjax:beforeSend', () => {
    $('#loading-overlay').show()
})

$('#profile').on('pjax:complete', () => {
    $('#loading-overlay').hide()
})

$("a[data-type='year'],a[data-type='month']").on('click', function () {
    const _this = $(this)
    const type = _this.data('type')
    $(`a[data-type='\${type}']`).removeAttr('data-selected')
    _this.attr('data-selected', true)
    $(_this.parents().get(1)).find('a[data-toggle="dropdown"] > span:first-child')?.text(_this.text())
    changeDateRange()
})
registerTopbarDropdown(document.getElementById('navbar-actions'))
JS;

$this->registerJs($js, View::POS_READY);
?>
