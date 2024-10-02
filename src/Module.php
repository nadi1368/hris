<?php

namespace hesabro\hris;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{

    public string | null $user = null;

    public string | null $settings = null;

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/hris/' . $category, $message, $params, $language);
    }
}
