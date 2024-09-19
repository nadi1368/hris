<?php

namespace hesabro\hris\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[InternalNumber]].
 *
 * @see InternalNumber
 */
class InternalNumberQuery extends ActiveQuery
{
    public function byId($id)
    {
        return $this->andWhere([InternalNumber::tableName() . '.id' => $id]);
    }

    public function byUserId($user_id)
    {
        return $this->andWhere([InternalNumber::tableName() . '.user_id' => $user_id]);
    }

    public function byName($name)
    {
        return $this->andWhere([$name, InternalNumber::tableName() . '.name', $name]);
    }

    public function byNumber($number)
    {
        return $this->andWhere([$number, InternalNumber::tableName() . '.number', $number]);
    }
}
