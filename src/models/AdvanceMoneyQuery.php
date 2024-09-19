<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[AdvanceMoney]].
 *
 * @see AdvanceMoney
 */
class AdvanceMoneyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AdvanceMoney[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AdvanceMoney|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AdvanceMoney::tableName().'.status', AdvanceMoney::STATUS_DELETED]);
    }

    public function my($userId)
    {
        return $this->andWhere(['user_id'=>$userId]);
    }
    public function wait()
    {
        return $this->andWhere(['status'=>AdvanceMoney::STATUS_WAIT_CONFIRM]);
    }
}
