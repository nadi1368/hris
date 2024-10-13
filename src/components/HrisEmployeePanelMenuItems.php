<?php

namespace hesabro\hris\components;

use hesabro\helpers\traits\MenuHelper;
use hesabro\hris\models\Content;
use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\Module;

class HrisEmployeePanelMenuItems
{
    use MenuHelper;

    public static function items()
    {
        $moduleId = Module::getInstance()->moduleId;

        return [
            [
                'label' => Module::t('module', 'Home') . ' ' . Module::t('module', 'HR'),
                'url' => ["/$moduleId/profile/index"],
                'group' => 'dashboard',
                'level' => 'first-level',
                'icon' => 'far fa-home'
            ],
            [
                'label' => 'دوره حقوق',
                'url' => ["/$moduleId/profile/salary-period"],
                'group' => 'salary-period',
                'level' => 'first-level',
                'icon' => 'fa fa-money-check'
            ],
            [
                'label' => 'حضور و غیاب',
                'url' => ["/$moduleId/profile/roll-call"],
                'group' => 'roll-call',
                'level' => 'first-level',
                'icon' => 'far fa-analytics'
            ],
            [
                'label' => 'قراردادها',
                'url' => ["/$moduleId/employee/user-contracts/my-contracts"],
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
                        'url' => ["/$moduleId/comfort/index"],
                        'group' => 'comfort',
                        'level' => 'second-level',
                        'icon' => 'far fa-gift'
                    ],
                    [
                        'label' => 'درخواست مرخصی',
                        'url' => ["/$moduleId/request-leave/index"],
                        'group' => 'requests',
                        'level' => 'second-level',
                        'icon' => 'far fa-clock'
                    ],
                    [
                        'label' => 'درخواست مساعده',
                        'url' => ["/$moduleId/advance-money/index"],
                        'group' => 'requests',
                        'level' => 'second-level',
                        'icon' => 'fa fa-money-bill'
                    ],
                    [
                        'label' => 'درخواست‌های رفاهی',
                        'url' => ["/$moduleId/comfort/items"],
                        'group' => 'comfort',
                        'level' => 'second-level',
                        'icon' => 'far fa-gift'
                    ],
                    [
                        'label' => Module::t('module', 'Request') . ' ' . Module::t('module', 'Letter'),
                        'url' => ["/$moduleId/employee/employee-request/my", 'type' => EmployeeRequest::TYPE_LETTER],
                        'group' => 'requests',
                        'level' => 'second-level',
                        'icon' => 'far fa-envelope'
                    ]
                ]
            ],
            [
                'label' => 'سایر',
                'group' => 'Others',
                // 'icon' => 'fa fa-table',
                'iconWidget' => 'ph:grid-four-bold',
                'iconWidgetClass' => 'font-24',
                'level' => 'first-level',
                'linkCssClass' => 'two-column',
                'items' => [
                    [
                        'label' => Content::itemAlias('Type', Content::TYPE_BUSINESS),
                        'url' => ["/$moduleId/faq/public?type=" . Content::TYPE_BUSINESS],
                        'group' => 'faq-business',
                        'level' => 'second-level',
                        'icon' => 'far fa-question'
                    ],
                    [
                        'label' => Content::itemAlias('Type', Content::TYPE_EMPLOYEE),
                        'url' => ["/$moduleId/faq/public?type=" . Content::TYPE_EMPLOYEE],
                        'group' => 'faq-employee',
                        'level' => 'second-level',
                        'icon' => 'fa fa-list-alt'
                    ],
                    [
                        'label' => Content::itemAlias('Type', Content::TYPE_JOB_DESCRIPTION),
                        'url' => ["/$moduleId/faq/public?type=" . Content::TYPE_JOB_DESCRIPTION],
                        'group' => 'faq-job-description',
                        'level' => 'second-level',
                        'icon' => 'fa fa-list-alt'
                    ],
                    [
                        'label' => Content::itemAlias('Type', Content::TYPE_ANNOUNCEMENT),
                        'url' => ["/$moduleId/faq/public?type=" . Content::TYPE_ANNOUNCEMENT],
                        'group' => 'faq-announcement',
                        'level' => 'second-level',
                        'icon' => 'fa fa-list-alt'
                    ],
                    [
                        'label' => Module::t('module', 'Internal Numbers'),
                        'icon' => 'far fa-phone',
                        'url' => ["/$moduleId/employee/internal-number/public"],
                        'group' => 'InternalNumber',
                        'level' => "second-level",
                    ],
                    [
                        'label' => Module::t('module', 'Organization Chart'),
                        'url' => "/$moduleId/employee/organization-chart/public",
                        'group' => 'organization-chart',
                        'level' => 'second-level',
                        'icon' => 'fa fa-id-card'
                    ],
                ]
            ]
        ];
    }
}
