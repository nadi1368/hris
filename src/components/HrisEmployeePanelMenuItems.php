<?php

namespace hesabro\hris\components;

use hesabro\helpers\traits\MenuHelper;
use hesabro\hris\models\EmployeeContent;
use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\Module;

class HrisEmployeePanelMenuItems
{
    use MenuHelper;

    public static function items($moduleId = null)
    {
        $moduleId = $moduleId ?: Module::getInstance()->id;

        return [
            [
                'label' => Module::t('module', 'Home') ,
                'url' => ["/$moduleId/employee-profile/index"],
                'group' => 'dashboard',
                'level' => 'first-level',
                'icon' => 'far fa-home'
            ],
            [
                'label' => 'دوره حقوق',
                'url' => ["/$moduleId/employee-profile/salary-period"],
                'group' => 'salary-period',
                'level' => 'first-level',
                'icon' => 'fa fa-money-check'
            ],
            [
                'label' => 'حضور و غیاب',
                'url' => ["/$moduleId/employee-profile/roll-call"],
                'group' => 'roll-call',
                'level' => 'first-level',
                'icon' => 'far fa-analytics'
            ],
            [
                'label' => 'قراردادها',
                'url' => ["/$moduleId/employee-profile/contracts"],
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
                        'url' => ["/$moduleId/employee-comfort"],
                        'group' => 'comfort',
                        'level' => 'second-level',
                        'icon' => 'far fa-gift'
                    ],
                    [
                        'label' => 'درخواست مرخصی',
                        'url' => ["/$moduleId/employee-request-leave/index"],
                        'group' => 'requests',
                        'level' => 'second-level',
                        'icon' => 'far fa-clock'
                    ],
                    [
                        'label' => 'درخواست مساعده',
                        'url' => ["/$moduleId/employee-advance-money/index"],
                        'group' => 'requests',
                        'level' => 'second-level',
                        'icon' => 'fa fa-money-bill'
                    ],
                    [
                        'label' => 'درخواست‌های رفاهی',
                        'url' => ["/$moduleId/employee-comfort/items"],
                        'group' => 'comfort',
                        'level' => 'second-level',
                        'icon' => 'far fa-gift'
                    ],
                    [
                        'label' => Module::t('module', 'Request') . ' ' . Module::t('module', 'Letter'),
                        'url' => ["/$moduleId/employee-request/my", 'type' => EmployeeRequest::TYPE_LETTER],
                        'group' => 'requests',
                        'level' => 'second-level',
                        'icon' => 'far fa-envelope'
                    ]
                ]
            ],
            [
                'label' => 'سایر',
                'group' => 'Others',
                'icon' => 'fa fa-table',
                'iconWidget' => 'ph:grid-four-bold',
                'iconWidgetClass' => 'font-24',
                'level' => 'first-level',
                'linkCssClass' => 'two-column',
                'items' => [
                    [
                        'label' => EmployeeContent::itemAlias('Type', EmployeeContent::TYPE_BUSINESS),
                        'url' => ["/$moduleId/employee-content/index?type=" . EmployeeContent::TYPE_BUSINESS],
                        'group' => 'faq-business',
                        'level' => 'second-level',
                        'icon' => 'far fa-question'
                    ],
                    [
                        'label' => EmployeeContent::itemAlias('Type', EmployeeContent::TYPE_REGULATIONS),
                        'url' => ["/$moduleId/employee-content/index?type=" . EmployeeContent::TYPE_REGULATIONS],
                        'group' => 'faq-employee',
                        'level' => 'second-level',
                        'icon' => 'fa fa-list-alt'
                    ],
                    [
                        'label' => EmployeeContent::itemAlias('Type', EmployeeContent::TYPE_JOB_DESCRIPTION),
                        'url' => ["/$moduleId/employee-content/index?type=" . EmployeeContent::TYPE_JOB_DESCRIPTION],
                        'group' => 'faq-job-description',
                        'level' => 'second-level',
                        'icon' => 'fa fa-list-alt'
                    ],
                    [
                        'label' => EmployeeContent::itemAlias('Type', EmployeeContent::TYPE_ANNOUNCEMENT),
                        'url' => ["/$moduleId/employee-content/index?type=" . EmployeeContent::TYPE_ANNOUNCEMENT],
                        'group' => 'faq-announcement',
                        'level' => 'second-level',
                        'icon' => 'fa fa-list-alt'
                    ],
                    [
                        'label' => Module::t('module', 'Internal Numbers'),
                        'icon' => 'far fa-phone',
                        'url' => ["/$moduleId/internal-number/public"],
                        'group' => 'InternalNumber',
                        'level' => "second-level",
                    ],
                    [
                        'label' => Module::t('module', 'Organization Chart'),
                        'url' => "/$moduleId/organization-chart/public",
                        'group' => 'organization-chart',
                        'level' => 'second-level',
                        'icon' => 'fa fa-id-card'
                    ],
                ]
            ]
        ];
    }
}
