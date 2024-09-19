<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use hesabro\hris\models\Comfort;

/**
 * ComfortSearch represents the model behind the search form of `hesabro\hris\models\Comfort`.
 */
class ComfortSearch extends Comfort
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'expire_time', 'status', 'type_limit', 'created', 'creator_id', 'update_id', 'changed'], 'integer'],
            [['title', 'description', 'additional_data'], 'safe'],
            [['amount_limit'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = Comfort::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
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
            'type' => $this->type,
            'expire_time' => $this->expire_time,
            'status' => $this->status,
            'type_limit' => $this->type_limit,
            'amount_limit' => $this->amount_limit,
            'created' => $this->created,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'additional_data', $this->additional_data]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param EmployeeBranchUser $employee
     * @return ActiveDataProvider
     */
    public function searchUser($params, EmployeeBranchUser $employee)
    {
        $query = Comfort::find()->canShow($employee);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
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
            'type' => $this->type,
            'expire_time' => $this->expire_time,
            'status' => $this->status,
            'type_limit' => $this->type_limit,
            'amount_limit' => $this->amount_limit,
            'created' => $this->created,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'additional_data', $this->additional_data]);

        return $dataProvider;
    }
}
