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
                'group' => 'settings',
                'url' => ["/$moduleId"],
            ],
            [
                'label' => 'اطلاعات اولیه',
                'iconWidget' => 'ph:database-bold',
                'group' => 'GeneralInfo',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => Module::t('module', "Salary Years Settings"),
                        'url' => ["/$moduleId/employee-branch/year-setting"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', 'Rate Of Year Salaries'),
                        'url' => ["/$moduleId/rate-of-year-salary/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Salary Insurances"),
                        'url' => ["/$moduleId/salary-insurance/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Workshop Insurances"),
                        'url' => ["/$moduleId/workshop-insurance/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Contract Templates"),
                        'url' => ["/$moduleId/contract-templates/index", 'type' => ContractTemplates::TYPE_CONTRACT],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "Letter Templates"),
                        'url' => ["/$moduleId/contract-templates/index", 'type' => ContractTemplates::TYPE_LETTER],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', "User Contracts Shelves"),
                        'url' => ["/$moduleId/user-contracts-shelves/index"],
                        'group' => 'GeneralInfo',
                    ],
                    [
                        'label' => Module::t('module', 'Job Description'),
                        'url' => ["/$moduleId/employee-content-manage/index", 'type' => EmployeeContent::TYPE_JOB_DESCRIPTION],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Notice'),
                        'url' => ["/$moduleId/employee-content-manage/index", 'type' => EmployeeContent::TYPE_ANNOUNCEMENT],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Employee'),
                        'url' => ["/$moduleId/employee-content-manage/index", 'type' => EmployeeContent::TYPE_REGULATIONS],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Internal Numbers'),
                        'url' => ["/$moduleId/internal-number/index"],
                        'group' => 'GeneralInfo'
                    ],
                    [
                        'label' => Module::t('module', 'Organization Chart'),
                        'url' => ["/$moduleId/organization-chart/index"],
                        'group' => 'GeneralInfo',
                    ],
                ]
            ],
            [
                'label' => 'حضور و غیاب',
                'icon' => 'far fa-analytics',
                'iconWidget' => 'ph:user-circle-check-bold',
                'group' => 'RollCall',
                'level' => "first-level",
                'items' => [
                    [
                        'label' => 'فایل های اکسل حضور و غیاب',
                        'url' => ["/$moduleId/employee-roll-call/list-csv"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'وضعیت تردد',
                        'url' => ["/$moduleId/employee-roll-call/index"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => Module::t('module', "Salary Items Additions"),
                        'url' => ["/$moduleId/salary-items-addition/index"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'فایل های اکسل مزایای غیر نقدی',
                        'url' => ["/$moduleId/salary-items-addition/list-csv-salary-non-cash"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'گزارش مرخصی کارمندان',
                        'url' => ["/$moduleId/salary-items-addition/report-leave"],
                        'group' => 'RollCall',
                    ],
                    [
                        'label' => 'نمودار مرخصی کارمندان',
                        'url' => ["/$moduleId/salary-items-addition/chart-leave"],
                        'group' => 'RollCall',
                    ],
                ]
            ],
            [
                'label' => Module::t('module', 'Comforts'),
                'iconWidget' => 'ph:gift-bold',
                'url' => ["/$moduleId/comfort/index"],
                'level' => "first-level",
                'group' => 'comforts',
            ],
            [
                'label' => Html::tag('span', Module::t('module', 'Requests'), ['class' => $advanceMoneyRequest || $employeeRequest || $comfortItemRequest ? 'pulse-notification' : '']),
                'iconWidget' => 'ph:gift-bold',
                'url' => ["/$moduleId/comfort-items/index"],
                'level' => "first-level",
                'group' => 'Requests',
                'encode' => false
            ],
            [
                'label' => Module::t('module', 'Employee Branches'),
                'icon' => 'far fa-building',
                'url' => ["/$moduleId/employee-branch/index"],
                'group' => 'EmployeeBranch',
                'level' => "first-level",
            ],
            [
                'label' => Html::tag('span', Module::t('module', "Employee Branch User"), ['class' => $employeePendingUpdate ? 'pulse-notification' : '']),
                'icon' => 'far fa-users',
                'url' => ["/$moduleId/employee-branch/users"],
                'group' => 'EmployeeBranchUser',
                'level' => "first-level",
                'encode' => false
            ],
            [
                'label' => Module::t('module', 'Salary Periods'),
                'icon' => 'far fa-money-check',
                'url' => ["/$moduleId/salary-period/index"],
                'group' => 'EmployeeSalaryPeriods',
                'level' => "first-level",
            ],
            [
                'label' => Module::t('module', 'Contracts'),
                'icon' => 'far fa-file-contract',
                'url' => ["/$moduleId/user-contracts/index"],
                'group' => 'EmployeeContracts',
                'level' => 'first-level'
            ],
            [
                'label' => 'مدیریت درخواست مرخصی',
                'group' => 'request-leave-manage',
                'level' => 'first-level',
                'icon' => 'fa fa-user',
                'items' => [
                    [
                        'label' => Module::t('module', 'Department Manager'),
                        'url' => ["/$moduleId/request-leave/manage"],
                        'group' => 'request-leave-manage',
                        'level' => 'second-level',
                    ],
                    [
                        'label' => Module::t('module', 'General Manager'),
                        'url' => ["/$moduleId/request-leave/admin"],
                        'group' => 'request-leave-manage',
                        'level' => 'second-level',
                    ]
                ]
            ],
        ];
    }
}