<?php

namespace hesabro\hris\models;

use yii\base\BaseObject;

/**
 * Class EmployeeHistory
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class EmployeeHistory extends BaseObject
{
    public $start_work;

    public $end_work;

    public $document_id_end_work;
}