<?php

namespace hesabro\hris\models;

/**
 * This is the ActiveQuery class for [[EmployeeContractTemplates]].
 *
 * @see EmployeeContractTemplates
 */
class ContractTemplatesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ContractTemplates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ContractTemplates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',ContractTemplates::tableName().'.status', ContractTemplates::STATUS_DELETED]);
    }

	public function byCreatorId($id)
	{
		return $this->andWhere([ContractTemplates::tableName().'.created_by' => $id]);
	}

	public function byUpdatedId($id)
	{
		return $this->andWhere([ContractTemplates::tableName().'.updated_by' => $id]);
	}

	public function byStatus($status)
	{
		return $this->andWhere([ContractTemplates::tableName().'.status' => $status]);
	}

	public function byId($id)
	{
		return $this->andWhere([ContractTemplates::tableName().'.id' => $id]);
	}
}
