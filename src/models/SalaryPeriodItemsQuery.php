<?php

namespace hesabro\hris\models;

use common\models\BalanceDetailed;
use common\models\Settings;

/**
 * This is the ActiveQuery class for [[SalaryPeriodItems]].
 *
 * @see SalaryPeriodItems
 */
class SalaryPeriodItemsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SalaryPeriodItems[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SalaryPeriodItems|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function bySalary()
    {
        return $this->joinWith(['period'])->andWhere(['OR',
            ['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_SALARY],
            'JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind") IS NULL'
        ]);
    }

    public function byReward()
    {
        return $this->joinWith(['period'])->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_REWARD]);
    }

    public function byYearKind()
    {
        return $this->joinWith(['period'])->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_YEAR]);
    }

    public function varianceAdvanceMoney()
    {
        return $this
            ->joinWith(['user.customer.oneAccount.balanceDetailed'])
            ->andWhere([BalanceDetailed::tableName() . '.definite_id' => Settings::get('m_debtor_advance_money')])
            ->andWhere(BalanceDetailed::tableName() . '.balance>0')
            ->andWhere(BalanceDetailed::tableName().'.`balance` <> '.SalaryPeriodItems::tableName().'.`advance_money`');
    }
    public function untilYear($endTime)
    {
        return $this->joinWith(['period'])
            ->andWhere(['<=', 'start_date', $endTime]);
    }

    public function byYear($startTime, $endTime)
    {
        return $this->joinWith(['period'])
            ->andWhere(['between', 'start_date', $startTime, $endTime]);
    }

    public function lastPayment($endTime)
    {
        return $this->bySalary()
            ->untilYear($endTime)
            ->andWhere(['>', 'basic_salary', 0])
            ->orderBy(['start_date' => SORT_DESC]);
    }

    public function byWorkShop($workShop)
    {
        return $this->joinWith(['period'])->andWhere(['workshop_id' => $workShop]);
    }


    /**
     * @param $userID
     * @return SalaryPeriodItemsQuery
     */
    public function byUser($userID) : SalaryPeriodItemsQuery
    {
        return $this->andWhere(['user_id' => $userID]);
    }

}
