<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[SalaryPeriod]].
 *
 * @see SalaryPeriod
 */
class SalaryPeriodQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SalaryPeriod[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SalaryPeriod|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byKind($kind)
    {
        return $this->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => $kind]);
    }

    public function bySalary()
    {
        return $this->andWhere(['OR',
            ['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_SALARY],
            'JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind") IS NULL'
        ]);
    }

    public function byWorkShop($workShop)
    {
        return $this->andWhere(['workshop_id' => $workShop]);
    }

    public function thisYear($startTimeYear, $endTimeYear)
    {
        return $this->andWhere(['between', 'start_date', $startTimeYear, $endTimeYear]);
    }

    public function byDate($date): self
    {
        $this->andWhere(['<=', 'start_date', $date])
            ->andWhere(['>=', 'end_date', $date]);

        return $this;
    }

    public function byStatus(int $status): self
    {
        $this->andWhere(['status' => $status]);

        return $this;
    }

    /**
     * @param $workshop_id
     * @param $start_date
     * @return SalaryPeriodQuery
     */
    public function byPrevious($workshop_id, $start_date): SalaryPeriodQuery
    {
        return $this->andWhere(['workshop_id' => $workshop_id])
            ->andWhere(['<', 'start_date', $start_date])->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_SALARY])
            ->orderBy(['start_date' => SORT_DESC]);
    }
}
