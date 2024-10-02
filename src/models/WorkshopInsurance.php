<?php

namespace hesabro\hris\models;

use common\models\Account;
use common\models\Branch;
use common\models\Year;

class WorkshopInsurance extends WorkshopInsuranceBase
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::class, ['id' => 'branch_id']);
    }

    public function canCreateYear()
    {
        return $this->getSalaryPeriod()
                ->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_YEAR])
                // ->andWhere(['between', 'start_date',strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian(Year::getDefault('start')) . ' 00:00:00'),strtotime(Year::getDefault('end') . ' 00:00:00')])
                ->andWhere(['between', 'start_date', Year::getDefault('startTime'), Year::getDefault('endTime')])
                ->limit(1)
                ->one() === null;
    }
}