<?php

namespace hesabro\hris\models;

use yii\db\ActiveQuery;
use common\models\Indicator\Indicator;

class EmployeeRequest extends EmployeeRequestBase
{
    public function getIndicator(): ActiveQuery
    {
        return $this->hasOne(Indicator::class, ['id' => 'indicator_id']);
    }
}