<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[EmployeeBranchUser]].
 *
 * @see EmployeeBranchUser
 */
class EmployeeBranchUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmployeeBranchUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmployeeBranchUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', EmployeeBranchUser::tableName() . '.status', EmployeeBranchUser::STATUS_DELETED]);
    }

    public function byRollCall($rollCallId)
    {
        return $this->andWhere(['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.roll_call_id")' => (int)$rollCallId]);
    }

    public function byNationalCode($nationalCode): EmployeeBranchUserQuery
    {
        return $this->andWhere(['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.nationalCode")' => (string)$nationalCode]);
    }

    public function byJobCode($jobCode)
    {
        return $this->andWhere(['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.job_code")' => (string)$jobCode]);
    }

    /**
     * @param $userId
     * @return EmployeeBranchUserQuery
     */
    public function byUserId($userId) : EmployeeBranchUserQuery
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    public function havePendingData(): self
    {
        return $this->andWhere(['NOT', [EmployeeBranchUser::tableName() . '.`pending_data`' => null]]);
    }
}
