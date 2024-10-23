<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class EmployeeContentSearch extends EmployeeContent
{
    /**
     * Determine if ignoring client_id for master queries
     *
     * @var bool
     */
    public $ignore_client;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scattered_search_query'], 'string'],
            [['id', 'type', 'status', 'created', 'creator_id', 'update_id', 'creator', 'sort'], 'integer'],
            [
                'type',
                'in',
                'range' => [
                    EmployeeContent::TYPE_CUSTOMER, EmployeeContent::TYPE_REGULATIONS,
                    EmployeeContent::TYPE_SOFTWARE,EmployeeContent::TYPE_BUSINESS,
                    EmployeeContent::TYPE_JOB_DESCRIPTION, EmployeeContent::TYPE_ANNOUNCEMENT
                ]
            ],
            [['ignore_client'], 'boolean'],
            [['title', 'description'], 'safe'],
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
    public function search($params, $pagination = ['pageSize' => 20])
    {
        $query = EmployeeContent::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort'=>[
                'defaultOrder' => [
                    'sort' => SORT_DESC
                ],
            ],
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
            'status' => $this->status,
            'created' => $this->created,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);


        if (!(Yii::$app->client->isMaster() && $this->ignore_client)) {
            $query->byClientAccess(Yii::$app->client->id);
        }

        if($this->scattered_search_query) {
            $query->byScatteredSearch($this->scattered_search_query);
        }

        return $dataProvider;
    }

    public function searchForEmployee($params, $pagination = ['pageSize' => 20])
    {
        $query = EmployeeContent::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort'=>[
                'defaultOrder' => [
                    'sort' => SORT_DESC
                ],
            ],
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
            'status' => $this->status,
            'created' => $this->created,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);


        if (!(Yii::$app->client->isMaster() && $this->ignore_client)) {
            $query->byClientAccess(Yii::$app->client->id);
        }

        $query->byCustomUserId(Yii::$app->user->identity->id);

        $jobTags = array_map(fn ($item) => $item, Customer::find()->findByUser(Yii::$app->user->identity->id)->one()->jobs ?: []);
        $query->byCustomJobTags($jobTags);

        if($this->scattered_search_query) {
            $query->byScatteredSearch($this->scattered_search_query);
        }

        return $dataProvider;
    }
}
