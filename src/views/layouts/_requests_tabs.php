<?php
/**
 * @var string $content
 */

use backend\models\AdvanceMoney;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\EmployeeRequest;
use yii\bootstrap4\Nav;
use yii\helpers\Html;

$advanceMoneyRequest = AdvanceMoney::find()->wait()->count();
$employeeRequestLetter = EmployeeRequest::find()->pending()->type(EmployeeRequest::TYPE_LETTER)->count();
$comfortItemRequest = ComfortItems::find()->waiting()->count();

Nav::begin([
    'options' => [
        'id' => 'employee-branch-user-view-user-nav',
        'class' => 'nav nav-tabs',
    ],
    'items' => [
        [
            'label'=> Html::tag('span', implode('', [
                Html::tag('span', Yii::t('app', 'Comforts')),
                $comfortItemRequest ? Html::tag('span', $comfortItemRequest, ['class' => 'badge badge-pill badge-danger badge-employee-tab']) : ''
            ]), ['class' => 'd-flex align-center justify-center gap-2']),
            'url' => ['/employee/comfort-items/index'],
            'linkOptions' => [
                'class' => 'nav-link ' . (Yii::$app->controller->id == 'comfort-items' ? 'active' : ''),
            ],
            'encode' => false
        ],
        [
            'label'=> Html::tag('span', implode('', [
                Html::tag('span', Yii::t('app', 'Advance Money')),
                $advanceMoneyRequest ? Html::tag('span', $advanceMoneyRequest, ['class' => 'badge badge-pill badge-danger badge-employee-tab']) : ''
            ]), ['class' => 'd-flex align-center justify-center gap-2']),
            'url' => ['/employee/advance-money-manage/index'],
            'linkOptions' => [
                'class' => 'nav-link ' . (Yii::$app->controller->action->id == 'advance-money-manage' ? 'active' : ''),
            ],
            'encode' => false
        ],
        [
            'label'=> Html::tag('span', implode('', [
                Html::tag('span', Yii::t('app', 'Letter')),
                $employeeRequestLetter ? Html::tag('span', $employeeRequestLetter, ['class' => 'badge badge-pill badge-danger badge-employee-tab']) : ''
            ]), ['class' => 'd-flex align-center justify-center gap-2']),
            'url' => ['/employee/employee-request/index', 'EmployeeRequestSearch' => ['type' => EmployeeRequest::TYPE_LETTER]],
            'linkOptions' => [
                'class' => 'nav-link ' . (Yii::$app->controller->action->id == 'employee-request' ? 'active' : ''),
            ],
            'encode' => false
        ],
        [
            'label'=> Html::tag('span', implode('', [
                Html::tag('span', Yii::t('app', 'Leave') . ' (' . Yii::t('app', 'Department Manager') . ')'),
                $employeeRequestLetter ? Html::tag('span', $employeeRequestLetter, ['class' => 'badge badge-pill badge-danger badge-employee-tab']) : ''
            ]), ['class' => 'd-flex align-center justify-center gap-2']),
            'url' => ['/request-leave/manage'],
            'linkOptions' => [
                'class' => 'nav-link ' . (Yii::$app->controller->id == 'request-leave' && Yii::$app->controller->action->id == 'manage' ? 'active' : ''),
            ],
            'encode' => false
        ],
        [
            'label'=> Html::tag('span', implode('', [
                Html::tag('span', Yii::t('app', 'Leave') . ' (' . Yii::t('app', 'General Manager') . ')'),
                $employeeRequestLetter ? Html::tag('span', $employeeRequestLetter, ['class' => 'badge badge-pill badge-danger badge-employee-tab']) : ''
            ]), ['class' => 'd-flex align-center justify-center gap-2']),
            'url' => ['/request-leave/admin'],
            'linkOptions' => [
                'class' => 'nav-link ' . (Yii::$app->controller->id == 'request-leave' && Yii::$app->controller->action->id == 'admin' ? 'active' : ''),
            ],
            'encode' => false
        ]
    ],
]);
Nav::end();

echo $content;
