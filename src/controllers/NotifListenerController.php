<?php

namespace hesabro\hris\controllers;

use hesabro\hris\Module;
use hesabro\notif\controllers\ListenerController;

class NotifListenerController extends ListenerController
{
    protected ?string $group = 'pulse';

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->setViewPath('@hesabro/notif/views/listener');

        $this->events = Module::getNotifEvents();
    }
}
