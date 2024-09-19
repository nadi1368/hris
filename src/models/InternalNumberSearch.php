<?php

namespace hesabro\hris\models;

use yii\data\ActiveDataProvider;

/**
 * InternalNumberSearch represents the model behind the search form of `hesabro\hris\models\InternalNumber`.
 */
class InternalNumberSearch extends InternalNumber
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'number', 'job_position'], 'string'],
            [['id', 'user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
        ];
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
        $query = InternalNumber::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort' => SORT_ASC]],
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
            'name' => $this->name,
            'number' => $this->number,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        // filter additional data
        $query->andFilterWhere([
            'like',
            'JSON_EXTRACT(' . InternalNumber::tableName() . '.`additional_data`, "$.job_position")',
            $this->job_position
        ]);

        return $dataProvider;
    }
}
