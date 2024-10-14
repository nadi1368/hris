<?php

namespace hesabro\hris;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public string $moduleId = 'hris';

    public string | null $user = null;

    public string | null $settings = null;

    public string | null $clientSettingsValue = null;

    public string | null $settingsSearch = null;

    public string | null $settingsCategory = null;

    public array | null $userFindUrl = ['/user/get-user-list'];

    public string | null $balanceDetailedClass = null;

    public string | null $balanceDailyClass = null;

    public array | null $employeeRole = ['employee'];

    public string | null $layoutPanel = null;

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/hris/' . $category, $message, $params, $language);
    }

    public static function createUrl($path = null) {
        $moduleId = self::getInstance()->moduleId;

        return "/$moduleId/$path";
    }
}
