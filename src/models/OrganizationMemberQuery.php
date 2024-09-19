<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[OrganizationMember]].
 *
 * @see OrganizationMember
 */
class OrganizationMemberQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * {@inheritdoc}
	 * @return OrganizationMember[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return OrganizationMember|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	public function byCreatorId($id)
	{
		return $this->andWhere([OrganizationMember::tableName() . '.created_by' => $id]);
	}

	public function byUpdatedId($id)
	{
		return $this->andWhere([OrganizationMember::tableName() . '.updated_by' => $id]);
	}

	public function byId($id)
	{
		return $this->andWhere([OrganizationMember::tableName() . '.id' => $id]);
	}

	public function byParentId($parent_id)
	{
		return $this->andWhere([OrganizationMember::tableName() . '.parent_id' => $parent_id]);
	}
	
	public function byUserId($user_id)
	{
		return $this->andWhere([OrganizationMember::tableName() . '.user_id' => $user_id]);
	}
}
