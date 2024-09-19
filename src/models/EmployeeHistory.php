<?php

namespace hesabro\hris\models;

use common\models\json\BaseModelJsonData;

/**
 * Class EmployeeHistory
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class EmployeeHistory extends BaseModelJsonData
{
    public $start_work;
    public $end_work;
    public $document_id_end_work;
}