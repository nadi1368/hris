<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[UserContracts]].
 *
 * @see UserContracts
 */
class UserContractsQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * {@inheritdoc}
	 * @return UserContracts[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return UserContracts|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	public function active()
	{
		return $this->onCondition(['<>', UserContracts::tableName() . '.status', UserContracts::STATUS_DELETED]);
	}

	public function byCreatorId($id)
	{
		return $this->andWhere([UserContracts::tableName() . '.created_by' => $id]);
	}

	public function byUpdatedId($id)
	{
		return $this->andWhere([UserContracts::tableName() . '.updated_by' => $id]);
	}

	public function byStatus($status)
	{
		return $this->andWhere([UserContracts::tableName() . '.status' => $status]);
	}

	public function byId($id)
	{
		return $this->andWhere([UserContracts::tableName() . '.id' => $id]);
	}

	public function byBranchId($branch_id)
	{
		return $this->andWhere([UserContracts::tableName() . '.branch_id' => $branch_id]);
	}

	public function byContractId($contract_id)
	{
		return $this->andWhere([UserContracts::tableName() . '.contract_id' => $contract_id]);
	}

	public function byUserId($user_id)
	{
		return $this->andWhere([UserContracts::tableName() . '.user_id' => $user_id]);
	}

	public function byEndDate($operand, $date)
	{
		return $this->andWhere([$operand, UserContracts::tableName() . '.end_date', $date]);
	}

	public function byStartDate($operand, $date)
	{
		return $this->andWhere([$operand, UserContracts::tableName() . '.start_date', $date]);
	}
}
