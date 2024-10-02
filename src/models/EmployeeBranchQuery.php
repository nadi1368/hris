<?php

namespace hesabro\hris\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[EmployeeBranch]].
 *
 * @see EmployeeBranch
 */
class EmployeeBranchQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmployeeBranch[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmployeeBranch|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',EmployeeBranch::tableName().'.status', EmployeeBranch::STATUS_DELETED]);
    }
}
