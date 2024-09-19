<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[SalaryBase]].
 *
 * @see SalaryBase
 */
class SalaryBaseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SalaryBase[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SalaryBase|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
