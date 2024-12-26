<?php

namespace hesabro\hris\controllers;

use hesabro\hris\Module;
use hesabro\rbac\controllers\PermissionController as BasePermissionController;

class PermissionController extends BasePermissionController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->moduleId = Module::getInstance()->rbacId;

        $this->setViewPath('@hesabro/rbac/views/permission');
    }
}
