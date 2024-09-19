<?php

namespace hesabro\hris\models;

use hesabro\hris\models\ComfortItems;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ComfortItemsSearch represents the model behind the search form of `hesabro\hris\models\ComfortItems`.
 */
class ComfortItemsSearch extends ComfortItems
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'comfort_id', 'user_id', 'status', 'created', 'creator_id', 'update_id', 'changed'], 'integer'],
            [['amount'], 'number'],
            [['attach', 'description', 'additional_data'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
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
        $query = ComfortItems::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]]
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
            'comfort_id' => $this->comfort_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'created' => $this->created,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'attach', $this->attach])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'additional_data', $this->additional_data]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchUser($params)
    {
        $query = ComfortItems::find()->byUser(Yii::$app->user->id);

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
            'comfort_id' => $this->comfort_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'created' => $this->created,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'attach', $this->attach])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'additional_data', $this->additional_data]);

        return $dataProvider;
    }

    public function lastComfortItemByUser(int $comfortId, int $userId): ?ComfortItems
    {
        return ComfortItems::find()
            ->andWhere([
                'user_id' => $userId,
                'comfort_id' => $comfortId,
                'status' => self::STATUS_CONFIRM
            ])->orderBy([
                'id' => SORT_DESC
            ])
            ->one();
    }
}
