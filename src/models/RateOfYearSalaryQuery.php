<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[RateOfYearSalary]].
 *
 * @see RateOfYearSalary
 */
class RateOfYearSalaryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RateOfYearSalary[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RateOfYearSalary|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', RateOfYearSalary::tableName() . '.status', RateOfYearSalary::STATUS_DELETED]);
    }

    /**
     * @param $year
     * @return RateOfYearSalaryQuery
     */
    public function byYear($year)
    {
        return $this->andWhere(['year' => $year])->limit(1);
    }
}
