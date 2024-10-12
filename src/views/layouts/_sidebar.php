<?php

use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeRequest;
use common\components\Menu;
use common\models\Faq;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$advanceMoneyRequest = AdvanceMoney::find()->wait()->exists();
$employeeRequest = EmployeeRequest::find()->pending()->exists();
$comfortItemRequest = ComfortItems::find()->waiting()->exists();
$employeePendingUpdate = EmployeeBranchUser::find()->havePendingData()->exists();

$menu_items = [
    [
        'label' => "داشبورد",
        'icon' => 'far fa-home',
        'group' => 'settings',
        'url' => ['/employee'],
    ],
    [
        'label' => 'اطلاعات اولیه',
        'icon' => 'far fa-layer-group',
        //'url' => ['/employee/default/index'],
        'group' => 'GeneralInfo',
        'level' => "first-level",
        'items' => [
            [
                'label' => Module::t('module', "Salary Years Settings"),
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/default/year-setting'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', 'Rate Of Year Salaries'),
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/rate-of-year-salary/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Salary Insurances"),
                'icon' => 'far fa-hashtag',
                'url' => ['/employee/salary-insurance/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Workshop Insurances"),
                'icon' => 'far fa-briefcase',
                'url' => ['/employee/workshop-insurance/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Contract Templates"),
                'icon' => 'far fa-file-contract',
                'url' => ['/employee/contract-templates/index', 'type' => ContractTemplates::TYPE_CONTRACT],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Letter Templates"),
                'icon' => 'far fa-file-contract',
                'url' => ['/employee/contract-templates/index', 'type' => ContractTemplates::TYPE_LETTER],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "User Contracts Shelves"),
                'icon' => 'far fa-file-contract',
                'url' => ['/employee/user-contracts-shelves/index'],
                'group' => 'GeneralInfo',
            ],
            [
                'label' => Module::t('module', "Faq Type")['job_description'],
                'icon' => 'far fa-file-contract',
                'url' => ['/faq/index', 'type' => Faq::TYPE_JOB_DESCRIPTION],
                'group' => 'GeneralInfo'
            ],
            [
                'label' => Module::t('module', "Faq Type")['announcement'],
                'icon' => 'far fa-file-contract',
                'url' => ['/faq/index', 'type' => Faq::TYPE_ANNOUNCEMENT],
                'group' => 'GeneralInfo'
            ],
            [
                'label' => Module::t('module', "Faq Type")['employee'],
                'icon' => 'far fa-file-contract',
                'url' => ['/faq/index', 'type' => Faq::TYPE_EMPLOYEE],
                'group' => 'GeneralInfo'
            ],
            [
                'label' => Module::t('module', "Internal Numbers"),
                'icon' => 'far fa-phone',
                'url' => ['/employee/internal-number/index'],
                'group' => 'GeneralInfo'
            ],
            [
                'label' => Module::t('module', "Organization Chart"),
                'icon' => 'far fa-id-card',
                'url' => ['/employee/organization-chart/index'],
                'group' => 'GeneralInfo',
            ],
        ]
    ],
    [
        'label' => 'حضور و غیاب',
        'icon' => 'far fa-analytics ',
        //'url' => ['/employee/default/index'],
        'group' => 'RollCall',
        'level' => "first-level",
        'items' => [
            [
                'label' => 'فایل های اکسل حضور و غیاب',
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/employee-roll-call/list-csv'],
                'group' => 'RollCall',
            ],
            [
                'label' => 'وضعیت تردد',
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/employee-roll-call/index'],
                'group' => 'RollCall',
            ],
            [
                'label' => Module::t('module', "Salary Items Additions"),
                'icon' => 'far fa-money-check',
                'url' => ['/employee/salary-items-addition/index'],
                'group' => 'RollCall',
            ],
            [
                'label' => 'فایل های اکسل مزایای غیر نقدی',
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/salary-items-addition/list-csv-salary-non-cash'],
                'group' => 'RollCall',
            ],
            [
                'label' => 'گزارش مرخصی کارمندان',
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/salary-items-addition/report-leave'],
                'group' => 'RollCall',
            ],
            [
                'label' => 'نمودار مرخصی کارمندان',
                'icon' => 'far fa-layer-group',
                'url' => ['/employee/salary-items-addition/chart-leave'],
                'group' => 'RollCall',
            ],
        ]
    ],
    [
        'label' => Module::t('module', "Comforts"),
        'icon' => 'far fa-gift ',
        'url' => ['/employee/comfort/index'],
        'level' => "first-level",
        'group' => 'comforts',
    ],
    [
        'label' => Html::tag('span', Module::t('module', 'Requests'), ['class' => $advanceMoneyRequest || $employeeRequest || $comfortItemRequest ? 'pulse-notification' : '']),
        'icon' => 'far fa-hand-paper',
        'url' => ['/employee/comfort-items/index'],
        'level' => "first-level",
        'group' => 'Requests',
        'encode' => false
    ],
    [
        'label' => Module::t('module', "Employee Branches"),
        'icon' => 'far fa-building',
        'url' => ['/employee/default/index'],
        'group' => 'EmployeeBranch',
        'level' => "first-level",
    ],
    [
        'label' => Html::tag('span', Module::t('module', "Employee Branch User"), ['class' => $employeePendingUpdate ? 'pulse-notification' : '']),
        'icon' => 'far fa-users',
        'url' => ['/employee/default/users'],
        'group' => 'EmployeeBranchUser',
        'level' => "first-level",
        'encode' => false
    ],
    [
        'label' => Module::t('module', "Salary Periods"),
        'icon' => 'far fa-money-check',
        'url' => ['/employee/salary-period/index'],
        'group' => 'EmployeeSalaryPeriods',
        'level' => "first-level",
    ],
    [
        'label' => Module::t('module', "Contracts"),
        'icon' => 'far fa-file-contract',
        'url' => ['/employee/user-contracts/index'],
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
                'url' => ['/request-leave/manage'],
                'group' => 'request-leave-manage',
                'level' => 'second-level',
                'icon' => 'fa fa-user'
            ],
            [
                'label' => Module::t('module', 'General Manager'),
                'url' => ['/request-leave/admin'],
                'group' => 'request-leave-manage',
                'level' => 'second-level',
                'icon' => 'fa fa-user'
            ]
        ]
    ],
];
?>
    <aside class="left-sidebar">
        <div class="mb-2">
            <?php $form = ActiveForm::begin([
                'id' => 'ajax-shortcut-sidebar',
                'action' => ['#']
            ]); ?>
            <input class="form-control rounded-0" type='text' id='search' placeholder='جستجو...'>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- Sidebar scroll-->
        <div class="scroll-sidebar">
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav">
                <?= Menu::widget(
                    [
                        'options' => ['id' => 'sidebarnav'],
                        'itemOptions' => ['class' => 'sidebar-item'],
                        'items' => $menu_items,
                    ]
                ) ?>
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
<?php
$script = <<<JS
var form_ajax =jQuery('#ajax-shortcut-sidebar');
form_ajax.on('beforeSubmit', function(e) {
    e.preventDefault();
    var key_current=$('#search').val();
    searchKeywordApp(key_current);
    $('#search').val('');
    return false;
});
$.extend($.expr[":"], {
"containsIN": function(elem, i, match, array) {
return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}
});

 $('#search').keyup(function(){
     // Search text
  var text = $(this).val();
 
  // Hide all content class element
  $('.sidebar-item').hide();
  $('.devider').hide(); 

  var sidebar_item_contains_text = $('.sidebar-item:containsIN("'+text+'")');
  // Search and show
  //show sidebar item contains text + nex div.devider
  sidebar_item_contains_text.show().next('.devider').show();
  
  sidebar_item_contains_text.parent().addClass('in');
  
  sidebar_item_contains_text.parent().prev().addClass('active');

    if(text.length === 0){
          $("#sidebarnav ul").removeClass('in');
          $("#sidebarnav a").removeClass('active');
    }
 });
JS;
$this->registerJs($script);

