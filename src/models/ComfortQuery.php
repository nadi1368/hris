<?php

namespace hesabro\hris\models;

use common\models\Customer;
use Yii;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Comfort]].
 *
 * @see Comfort
 */
class ComfortQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Comfort[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Comfort|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', Comfort::tableName() . '.status', Comfort::STATUS_DELETED]);
    }

    /**
     * @param $time
     * @return ComfortQuery
     */
    public function notExpire($time): ComfortQuery
    {
        return $this->andWhere(['OR', ['>=', 'expire_time', $time], ['=', 'expire_time', 0]]);
    }

    /**
     * @param EmployeeBranchUser $employee
     * @return ComfortQuery
     */
    public function canShow(EmployeeBranchUser $employee): ComfortQuery
    {
        $jobTags = array_map(fn ($item) => $item, Customer::find()->findByUser($employee->user_id)->one()->jobs ?: []);
        $this->notExpire(time())
            ->assignMe($employee->user_id)
            ->byCustomJobTags($jobTags)
            ->byExcludedCustomJobTags($jobTags);
        /**
        if($employee->marital == EmployeeBranchUser::MARITAL_SINGLE)
        {
            // اگر مجرد باشد خدمات ویژه متاهلین نمایش داده نمیشود
            $this->checkMarried();
        }
        */
        return $this;
    }

    /**
     * @param int $userId
     * @return ComfortQuery
     */
    public function assignMe(int $userId): ComfortQuery
    {
        return $this->andWhere(['OR',
            ["JSON_CONTAINS(" . Comfort::tableName() . ".additional_data, JSON_OBJECT('users', '$userId'))" => 1],
            ["JSON_EXTRACT(" . Comfort::tableName() . ".additional_data, '$.users')" => new Expression("json_array()")],
        ])->andWhere(['OR',
            new Expression("(NOT JSON_CONTAINS(" . Comfort::tableName() . ".additional_data, JSON_QUOTE('$userId'), '$.excluded_users'))"),
            ["JSON_EXTRACT(" . Comfort::tableName() . ".additional_data, '$.excluded_users')" => new Expression("json_array()")],
        ]);
    }


    public function byCustomJobTags($tags): self
    {
        return $this->andWhere(['OR',
            ['JSON_OVERLAPS(additional_data->"$.jobs", \'' . json_encode($tags) . '\')' => 1],
            ['JSON_LENGTH(JSON_EXTRACT(additional_data, "$.jobs"))' => 0]
        ]);
    }

    public function byExcludedCustomJobTags($tags): self
    {
        return $this->andWhere(['OR',
            ['JSON_OVERLAPS(additional_data->"$.excluded_jobs", \'' . json_encode($tags) . '\')' => 0],
            ['JSON_LENGTH(JSON_EXTRACT(additional_data, "$.excluded_jobs"))' => 0]
        ]);
    }

    /**
     * @return ComfortQuery
     */
    public function checkMarried(): ComfortQuery
    {
        // اگر مجرد باشد خدمات ویژه متاهلین نمایش داده نمیشود
        return $this->andWhere(["JSON_EXTRACT(" . Comfort::tableName() . ".additional_data, '$.married')" => new Expression('false')]);
    }
}
