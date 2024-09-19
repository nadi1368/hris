<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[SalaryInsurance]].
 *
 * @see SalaryInsurance
 */
class SalaryInsuranceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SalaryInsurance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SalaryInsurance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
