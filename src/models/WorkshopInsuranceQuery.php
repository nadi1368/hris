<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[WorkshopInsurance]].
 *
 * @see WorkshopInsurance
 */
class WorkshopInsuranceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return WorkshopInsurance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return WorkshopInsurance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
