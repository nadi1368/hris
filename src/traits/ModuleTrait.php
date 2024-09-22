<?php

namespace hesabro\hris\traits;

use hesabro\hris\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package hesabro\hris\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('hris');
    }

}
