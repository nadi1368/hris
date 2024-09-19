<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[SalaryItemsAddition]].
 *
 * @see SalaryItemsAddition
 */
class SalaryItemsAdditionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SalaryItemsAddition[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SalaryItemsAddition|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', SalaryItemsAddition::tableName() . '.status', SalaryItemsAddition::STATUS_DELETED]);
    }

    public function confirm()
    {
        return $this->andWhere([SalaryItemsAddition::tableName() . '.status' => SalaryItemsAddition::STATUS_CONFIRM]);
    }

    public function byPeriod($fromTime, $endTime)
    {
        return $this->andWhere(['between', 'from_date', $fromTime, $endTime]);
    }

    public function byUser($userId)
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    public function byKind($kind)
    {
        return $this->andWhere(['kind' => $kind]);
    }

    public function byType($type)
    {
        return $this->andWhere(['type' => $type]);
    }

    public function bySalary($userId, $kind, $type, $fromTime, $endTime)
    {
        return $this->andWhere(['user_id' => $userId])
            ->andWhere(['kind' => $kind])
            ->andFilterWhere(['type' => $type])
            ->andFilterWhere(['between', 'from_date', $fromTime, $endTime])
            ->andWhere(['status'=>SalaryItemsAddition::STATUS_CONFIRM]);
    }
}
