<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[UserContractsShelves]].
 *
 * @see UserContractsShelves
 */
class UserContractsShelvesQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * {@inheritdoc}
	 * @return UserContractsShelves[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return UserContractsShelves|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	public function active()
	{
		return $this->onCondition(['<>', UserContractsShelves::tableName() . '.status', UserContractsShelves::STATUS_DELETED]);
	}

	public function byCreatorId($id)
	{
		return $this->andWhere([UserContractsShelves::tableName() . '.created_by' => $id]);
	}

	public function byUpdatedId($id)
	{
		return $this->andWhere([UserContractsShelves::tableName() . '.updated_by' => $id]);
	}

	public function byStatus($status)
	{
		return $this->andWhere([UserContractsShelves::tableName() . '.status' => $status]);
	}

	public function byId($id)
	{
		return $this->andWhere([UserContractsShelves::tableName() . '.id' => $id]);
	}

	public function empty()
	{
		$this->select([
			UserContractsShelves::tableName() . '.*',
			'(select COUNT(*) from ' . UserContracts::tableName() . ' where shelf_id = ' . UserContractsShelves::tableName() . '.id and ' . UserContracts::tableName() . '.status = ' . UserContracts::STATUS_CONFIRM . ') as contracts_count',
		]);
		return $this->andHaving(UserContractsShelves::tableName() . '.capacity > contracts_count');
	}
}
