<?php

namespace hesabro\hris;

use hesabro\hris\models\AdvanceMoneyBase;
use hesabro\hris\models\ComfortItemsBase;
use hesabro\hris\models\EmployeeBranchUserBase;
use hesabro\hris\models\RequestLeave;
use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\Url;

class Module extends BaseModule
{
    public string | null $user = null;

    public string | null $settings = null;

    public string | null $clientSettingsValue = null;

    public string | null $settingsSearch = null;

    public string | null $settingsCategory = null;

    public string | null $assignment = null;

    public array $hiringAttachRoles = [];

    public array $hiringDetachRoles = [];

    public string | null $productLogo = null;

    public array | null $userFindUrl = ['/user/get-user-list'];

    public string | null $userViewUrl = '/user-main/view-ajax';

    public string | null $balanceDetailedClass = null;

    public string | null $balanceDailyClass = null;

    public array | null $employeeRole = ['employee'];

    public string | null $layoutPanel = null;

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('hesabro/hris/' . $category, $message, $params, $language);
    }

    public static function createUrl(string $path = null, array $params = [])
    {
        $moduleId = self::getInstance()?->id;

        $path = trim($path ?: '', '/');
        return Url::to([rtrim("/$moduleId/$path", '/'), ...$params]);
    }

    public static function getNotifEvents(): array
    {
        return array_merge(
            AdvanceMoneyBase::itemAlias('Notif') ?: [],
            RequestLeave::itemAlias('Notif') ?: [],
            EmployeeBranchUserBase::itemAlias('Notif') ?: [],
            ComfortItemsBase::itemAlias('Notif') ?: [],
        );
    }
}
