<?php

namespace hesabro\hris;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{

    public string | null $user = null;

    public string | null $settings = null;

    public array $modelMap = [];

    public function init(): void
    {
        parent::init();
    }
}