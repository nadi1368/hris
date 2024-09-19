<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[EmployeeRollCall]].
 *
 * @see EmployeeRollCall
 */
class EmployeeRollCallQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmployeeRollCall[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmployeeRollCall|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
