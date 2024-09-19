<?php

namespace hesabro\hris\models;

use backend\models\User;
use yii\db\ActiveQuery;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;

/**
 * @mixin SoftDeleteQueryBehavior
 */
class EmployeeRequestQuery extends ActiveQuery
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'softDelete' => [
                'class' => SoftDeleteQueryBehavior::class
            ]
        ]);
    }

    public function byEmployee(EmployeeBranchUser $employee): self
    {
        $this->andWhere(['user_id' => $employee->user_id]);

        return $this;
    }

    public function byUser(User $user): self
    {
        $this->andWhere(['user_id' => $user->id]);

        return $this;
    }

    public function pending(): self
    {
        $this->andWhere(['status' => EmployeeRequest::STATUS_PENDING]);

        return $this;
    }

    public function reject(): self
    {
        $this->andWhere(['status' => EmployeeRequest::STATUS_REJECT]);

        return $this;
    }

    public function accept(): self
    {
        $this->andWhere(['status' => EmployeeRequest::STATUS_ACCEPT]);

        return $this;
    }

    public function type(int $type): self
    {
        $this->andWhere(['type' => $type]);

        return $this;
    }
}
