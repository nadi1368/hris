<?php

namespace hesabro\hris\components;

use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\EmployeeContent;
use hesabro\hris\models\ContractTemplates;
use hesabro\hris\Module;
use yii\helpers\Html;

class HrisAdminPanelMenuItems
{


    public static function items($moduleId = null)
    {
        $moduleId = $moduleId ?: Module::getInstance()->id;
        $advanceMoneyRequest = AdvanceMoney::find()->wait()->exists();
        //$employeeRequest = EmployeeRequest::find()->pending()->exists();
        $employeeRequest = false;
        $comfortItemRequest = ComfortItems::find()->waiting()->exists();
        //$employeePendingUpdate = EmployeeBranchUser::find()->havePendingData()->exists();
        $employeePendingUpdate = false;

        return [
            [
                'label' => "داشبورد",
                'iconWidget' => 'ph:house-bold',
                'icon' => 'far fa-home',
                'group' => 'settings',
                'url' => ["/$moduleId"],
            ],
            [
                'label' => 'تنظیمات',
                'iconWidget' => 'ph:settings-bold',
                'icon' => 'far fa-cog',
                'group' => 'GeneralSettings',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => Module::t('module', "Salary Years Settings"),
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/employee-branch/year-setting"],
                        'group' => 'GeneralSettings',
                    ],
                    [
                        'label' => Module::t('module', 'Rate Of Year Salaries'),
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/rate-of-year-salary/index"],
                        'group' => 'GeneralSettings',
                    ],
                    [
                        'label' => Module::t('module', "Salary Insurances"),
                        'icon' => 'far fa-hashtag',
                        'url' => ["/$moduleId/salary-insurance/index"],
                        'group' => 'GeneralSettings',
                    ],
                    [
                        'label' => Module::t('module', "Workshop Insurances"),
                        'icon' => 'far fa-briefcase',
                        'url' => ["/$moduleId/workshop-insurance/index"],
                        'group' => 'GeneralSettings',
                    ],
                ]
            ],
            [
                'label' => 'اطلاعات',
                'iconWidget' => 'ph:database-bold',
                'icon' => 'far fa-layer-group',
                'group' => 'GeneralInfo',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => Module::t('module', "Contract Templates"),
                        'icon' => 'far fa-file-contract',
                        'url' => ["/$moduleId/contract-templates/index", 'type' => ContractTemplates::TYPE_CONTRACT],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Letter Templates"),
                        'icon' => 'far fa-file-contract',
                        'url' => ["/$moduleId/contract-templates/index", 'type' => ContractTemplates::TYPE_LETTER],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "User Contracts Shelves"),
                        'icon' => 'far fa-file-contract',
                        'url' => ["/$moduleId/user-contracts-shelves/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', 'Employ Description'),
                        'icon' => 'far fa-file-contract',
                        'url' => ["/$moduleId/employee-content-manage/index", 'type' => EmployeeContent::TYPE_JOB_DESCRIPTION],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Notices'),
                        'icon' => 'far fa-file-contract',
                        'url' => ["/$moduleId/employee-content-manage/index", 'type' => EmployeeContent::TYPE_ANNOUNCEMENT],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Regulations'),
                        'icon' => 'far fa-file-contract',
                        'url' => ["/$moduleId/employee-content-manage/index", 'type' => EmployeeContent::TYPE_REGULATIONS],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Internal Numbers'),
                        'icon' => 'far fa-phone',
                        'url' => ["/$moduleId/internal-number/index"],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Organization Chart'),
                        'icon' => 'far fa-id-card',
                        'url' => ["/$moduleId/organization-chart/index"],
                        'group' => 'GeneralInfo',
                    ],
                ]
            ],
            [
                'label' => 'حضور و غیاب',
                'icon' => 'far fa-analytics ',
                'iconWidget' => 'ph:user-circle-check-bold',
                'group' => 'RollCall',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => 'فایل های اکسل حضور و غیاب',
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/employee-roll-call/list-csv"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'وضعیت تردد',
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/employee-roll-call/index"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => Module::t('module', "Salary Items Additions"),
                        'icon' => 'far fa-money-check',
                        'url' => ["/$moduleId/salary-items-addition/index"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'فایل های اکسل مزایای غیر نقدی',
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/salary-items-addition/list-csv-salary-non-cash"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'گزارش مرخصی کارمندان',
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/salary-items-addition/report-leave"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'نمودار مرخصی کارمندان',
                        'icon' => 'far fa-layer-group',
                        'url' => ["/$moduleId/salary-items-addition/chart-leave"],
                        'group' => 'RollCall',
                    ],
                ]
            ],
            [
                'label' => Module::t('module', 'Comforts'),
                'iconWidget' => 'ph:gift-bold',
                'icon' => 'far fa-gift ',
                'url' => ["/$moduleId/comfort/index"],
                'group' => 'comforts',
            ],
            [
                'label' => Html::tag('span', Module::t('module', 'Requests'), ['class' => $advanceMoneyRequest || $employeeRequest || $comfortItemRequest ? 'pulse-notification' : '']),
                'icon' => 'far fa-hand-paper',
                'iconWidget' => 'ph:gift-bold',
                'url' => ["/$moduleId/comfort-items/index"],
                'group' => 'Requests',
                'encode' => false
            ],
            [
                'label' => Module::t('module', 'Employee Branches'),
                'icon' => 'far fa-building',
                'url' => ["/$moduleId/employee-branch/index"],
                'group' => 'EmployeeBranch',
            ],
            [
                'label' => Html::tag('span', Module::t('module', "Employee Branch User"), ['class' => $employeePendingUpdate ? 'pulse-notification' : '']),
                'icon' => 'far fa-users',
                'url' => ["/$moduleId/employee-branch/users"],
                'group' => 'EmployeeBranchUser',
                'encode' => false
            ],
            [
                'label' => Module::t('module', 'Salary Periods'),
                'icon' => 'far fa-money-check',
                'url' => ["/$moduleId/salary-period/index"],
                'group' => 'EmployeeSalaryPeriods',
            ],
            [
                'label' => Module::t('module', 'Contracts'),
                'icon' => 'far fa-file-contract',
                'url' => ["/$moduleId/user-contracts/index"],
                'group' => 'EmployeeContracts',
            ],
        ];
    }
}