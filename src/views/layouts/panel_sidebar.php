<?php

use hesabro\hris\models\EmployeeRequest;
use common\components\Menu;
use common\models\Faq;
use common\models\Comments;
use common\models\Rbac;
use yii\helpers\Html;
use yii\helpers\Url;


$count_tickets_waiting = 0;

if (Yii::$app->user->identity->canAccess(['comments/inbox', 'comments/create'])) {
    $count_tickets_waiting = Comments::countInbox(exist: true);
}

$menuItems = [
    [
        'label' => Yii::t('app' , 'Home') . ' ' . Yii::t('app', 'HR'),
        'url' => ['/profile/index'],
        'group' => 'dashboard',
        'level' => 'first-level',
        'icon' => 'far fa-home'
    ],
    [
        'label' => 'دوره حقوق',
        'url' => ['/profile/salary-period'],
        'group' => 'salary-period',
        'level' => 'first-level',
        'icon' => 'fa fa-money-check'
    ],
    [
        'label' => 'حضور و غیاب',
        'url' => ['/profile/roll-call'],
        'group' => 'roll-call',
        'level' => 'first-level',
        'icon' => 'far fa-analytics'
    ],
    [
        'label' => 'قراردادها',
        'url' => ['/employee/user-contracts/my-contracts'],
        'group' => 'contract',
        'level' => 'first-level',
        'icon' => 'far fa-handshake'
    ],
    [
        'label' => 'درخواست‌ها',
        'group' => 'requests',
        'level' => 'first-level',
        'icon' => 'fas fa-sticky-note',
        'items' => [
            [
                'label' => 'لیست امکانات رفاهی',
                'url' => ['/comfort/index'],
                'group' => 'comfort',
                'level' => 'second-level',
                'icon' => 'far fa-gift'
            ],
            [
                'label' => 'درخواست مرخصی',
                'url' => ['/request-leave/index'],
                'group' => 'requests',
                'level' => 'second-level',
                'icon' => 'far fa-clock'
            ],
            [
                'label' => 'درخواست مساعده',
                'url' => ['/advance-money/index'],
                'group' => 'requests',
                'level' => 'second-level',
                'icon' => 'fa fa-money-bill'
            ],
            [
                'label' => 'درخواست‌های رفاهی',
                'url' => ['/comfort/items'],
                'group' => 'comfort',
                'level' => 'second-level',
                'icon' => 'far fa-gift'
            ],
            [
                'label' => Yii::t('app', 'Request') . ' ' . Yii::t('app', 'Letter'),
                'url' => ['/employee/employee-request/my', 'type' => EmployeeRequest::TYPE_LETTER],
                'group' => 'requests',
                'level' => 'second-level',
                'icon' => 'far fa-envelope'
            ]
        ]
    ],
    // [
    //     'label' => Yii::t('app', 'Faq'),
    //     'group' => 'faq',
    //     'level' => 'first-level',
    //     'icon' => 'fa fa-question',
    //     'items' => [
    //         [
    //             'label' => Faq::itemAlias('Type', Faq::TYPE_BUSINESS),
    //             'url' => ['/faq/public?type=' . Faq::TYPE_BUSINESS],
    //             'group' => 'faq',
    //             'level' => 'second-level',
    //             'icon' => 'fa fa-question'
    //         ],
    //         [
    //             'label' => Faq::itemAlias('Type', Faq::TYPE_EMPLOYEE),
    //             'url' => ['/faq/public?type=' . Faq::TYPE_EMPLOYEE],
    //             'group' => 'faq',
    //             'level' => 'second-level',
    //             'icon' => 'fa fa-question'
    //         ]
    //     ]
    // ],
    [
        'label' => Faq::itemAlias('Type', Faq::TYPE_BUSINESS),
        'url' => ['/faq/public?type=' . Faq::TYPE_BUSINESS],
        'group' => 'faq-business',
        'level' => 'first-level',
        'icon' => 'far fa-question'
    ],
    [
        'label' => Faq::itemAlias('Type', Faq::TYPE_EMPLOYEE),
        'url' => ['/faq/public?type=' . Faq::TYPE_EMPLOYEE],
        'group' => 'faq-employee',
        'level' => 'first-level',
        'icon' => 'fa fa-list-alt'
    ],
    [
        'label' => Faq::itemAlias('Type', Faq::TYPE_JOB_DESCRIPTION),
        'url' => ['/faq/public?type=' . Faq::TYPE_JOB_DESCRIPTION],
        'group' => 'faq-job-description',
        'level' => 'first-level',
        'icon' => 'fa fa-list-alt'
    ],
    [
        'label' => Faq::itemAlias('Type', Faq::TYPE_ANNOUNCEMENT),
        'url' => ['/faq/public?type=' . Faq::TYPE_ANNOUNCEMENT],
        'group' => 'faq-announcement',
        'level' => 'first-level',
        'icon' => 'fa fa-list-alt'
    ],
    [

        'label' => $count_tickets_waiting ?
            Html::tag('div', Html::tag('span', Yii::t("app", "Tickets"), ['class' => '']) .
                Html::tag('span', '*', ['class' => 'badge badge-danger mr-2 font-14', 'style' => 'right: 100px; position: relative;']),
                [
                    'class' => 'd-flex justify-content-between align-items-baseline'
                ]) : Rbac::itemAlias('Type', Rbac::TYPE_INBOX),
        'url' => ['/employee/ticket/inbox'],
        'group' => 'tickets',
        'level' => 'first-level',
        'icon' => 'far fa-headset'
    ],
    [
        'label' => Yii::t("app", "Internal Numbers"),
        'icon' => 'far fa-phone',
        'url' => ['/employee/internal-number/public'],
        'group' => 'InternalNumber',
        'level' => "first-level",
    ],
    [
        'label' => Yii::t('app', 'Organization Chart'),
        'url' => '/employee/organization-chart/public',
        'group' => 'organization-chart',
        'level' => 'first-level',
        'icon' => 'fa fa-id-card'
    ],
    [
        'label' => Rbac::itemAlias('Type', Rbac::TYPE_EMPLOYEE),
        'iconAddress' => Url::to('@web/module/salary-wage/img/users-solid.svg'),
        'group' => 'Employee',
        'options'=>['class'=> 'bg-success sidebar-item'],
        'level' => "first-level",
        'url' => ['/employee'],
        'visible' => Yii::$app->user->identity->canAccess('EmployeeBranch/index')
    ],
];
?>

<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav pt-2">
            <?= Menu::widget(
                [
                    'options' => ['id' => 'sidebarnav'],
                    'itemOptions' => ['class' => 'sidebar-item'],
                    'items' => $menuItems,
                    'encodeLabels' => false,
                ]
            ) ?>
        </nav>
    </div>
</aside>