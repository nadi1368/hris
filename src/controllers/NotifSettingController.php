<?php

namespace hesabro\hris\controllers;

use hesabro\hris\Module;
use hesabro\notif\controllers\SettingController;

class NotifSettingController extends SettingController
{
    protected ?string $group = 'pulse';

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->setViewPath('@hesabro/notif/views/setting');

        $this->events = Module::getNotifEvents();
    }
}