<?php

namespace hesabro\hris\controllers;

use hesabro\hris\Module;
use hesabro\rbac\controllers\RoleController as BaseRoleController;

class RoleController extends BaseRoleController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->moduleId = Module::getInstance()->rbacId;

        $this->setViewPath('@hesabro/rbac/views/role');
    }
}