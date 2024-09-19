<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrganizationMemberSearch represents the model behind the search form of `hesabro\hris\models\OrganizationMember`.
 */
class OrganizationMemberSearch extends OrganizationMember
{
	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['id', 'slave_id', 'parent_id', 'user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
			[['name',], 'string'],
			[['show_internal_number',], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios()
	{
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = OrganizationMember::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'user_id' => $this->user_id,
			'name' => $this->name,
			'show_internal_number' => $this->show_internal_number,
			'parent_id' => $this->parent_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'created_by' => $this->created_by,
			'updated_by' => $this->updated_by,
		]);

		return $dataProvider;
	}
}
