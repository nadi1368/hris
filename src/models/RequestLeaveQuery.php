<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[RequestLeave]].
 *
 * @see RequestLeave
 */
class RequestLeaveQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RequestLeave[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RequestLeave|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',RequestLeave::tableName().'.status', RequestLeave::STATUS_DELETED]);
    }

    public function my($userId)
    {
        return $this->andWhere(['user_id'=> $userId]);
    }

    public function manage($userId)
    {
        return $this->andWhere(['manager_id'=> $userId]);
    }
}
