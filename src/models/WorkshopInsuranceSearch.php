<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use hesabro\hris\models\WorkshopInsurance;

/**
 * WorkshopInsuranceSearch represents the model behind the search form of `hesabro\hris\models\WorkshopInsurance`.
 */
class WorkshopInsuranceSearch extends WorkshopInsurance
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['code', 'title', 'manager', 'additional_data'], 'safe'],
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
        $query = WorkshopInsurance::find();

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
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'manager', $this->manager])
            ->andFilterWhere(['like', 'additional_data', $this->additional_data]);

        return $dataProvider;
    }
}
