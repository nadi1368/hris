<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use hesabro\hris\models\SalaryBase;

/**
 * SalaryBaseSearch represents the model behind the search form of `hesabro\hris\models\SalaryBase`.
 */
class SalaryBaseSearch extends SalaryBase
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['year', 'group'], 'safe'],
            [['cost_of_year', 'cost_of_work', 'cost_of_hours'], 'number'],
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
        $query = SalaryBase::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
            'cost_of_year' => $this->cost_of_year,
            'cost_of_work' => $this->cost_of_work,
            'cost_of_hours' => $this->cost_of_hours,
        ]);

        $query->andFilterWhere(['like', 'year', $this->year])
            ->andFilterWhere(['like', 'group', $this->group]);

        return $dataProvider;
    }
}
