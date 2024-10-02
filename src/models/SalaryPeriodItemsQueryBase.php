<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[SalaryPeriodItems]].
 *
 * @see SalaryPeriodItems
 */
class SalaryPeriodItemsQueryBase extends \yii\db\ActiveQuery
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
     * @return self
     */
    public function byUser($userID): self
    {
        return $this->andWhere(['user_id' => $userID]);
    }

}
