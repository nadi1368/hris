<?php

namespace hesabro\hris\models;

use hesabro\helpers\validators\DateValidator;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SalaryItemsAdditionSearch represents the model behind the search form of `hesabro\hris\models\SalaryItemsAddition`.
 */
class SalaryItemsAdditionSearchBase extends SalaryItemsAddition
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'second', 'status', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['description', 'kind'], 'safe'],
            [['from_date', 'to_date'], DateValidator::class],
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
        $query = SalaryItemsAddition::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder'=>['id'=>SORT_DESC]]
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
            'kind' => $this->kind,
            'type' => $this->type,
            'second' => $this->second,
            'status' => $this->status,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['like', 'description', $this->description]);

        if ($this->from_date) {
            $from_date = Yii::$app->jdf::jalaliToTimestamp($this->from_date,"Y/m/d");
            $query->andFilterWhere(['>=', 'from_date', $from_date]);
        }
        if ($this->to_date) {
            $to_date = Yii::$app->jdf::jalaliToTimestamp($this->to_date,"Y/m/d");
            $query->andFilterWhere(['<=', 'from_date', $to_date]);
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchReportLeave($params)
    {
        $kindHourly = SalaryItemsAddition::KIND_LEAVE_HOURLY;
        $query = SalaryItemsAddition::find()
            ->select([
                SalaryItemsAddition::tableName() . '.*',
                "SUM(CASE WHEN kind=$kindHourly THEN (`second`/(8*60)) ELSE ((`to_date`-`from_date`)/(24*60*60)) END) as total",

                "SUM(CASE WHEN type=".SalaryItemsAddition::TYPE_LEAVE_MERIT_HOURLY." THEN (`second`/(60)) ELSE 0 END) as total_merit_hourly",

                "SUM(CASE WHEN type=".SalaryItemsAddition::TYPE_LEAVE_MERIT_DAILY." THEN ((`to_date`-`from_date`)/(24*60*60)) ELSE 0 END) as total_merit_daily",
                "SUM(CASE WHEN type=".SalaryItemsAddition::TYPE_LEAVE_TREATMENT_DAILY." THEN ((`to_date`-`from_date`)/(24*60*60)) ELSE 0 END) as total_treatment_daily",
                "SUM(CASE WHEN type=".SalaryItemsAddition::TYPE_LEAVE_NO_SALARY_DAILY." THEN ((`to_date`-`from_date`)/(24*60*60)) ELSE 0 END) as total_no_salary_daily",
            ])
            ->groupBy(['user_id'])
            ->andWhere(['IN', 'kind', [SalaryItemsAddition::KIND_LEAVE_DAILY, SalaryItemsAddition::KIND_LEAVE_HOURLY]])
            ->confirm();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['total' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['total'] = [
            'asc' => ['total' => SORT_ASC],
            'desc' => ['total' => SORT_DESC],
        ];

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
            'kind' => $this->kind,
            'type' => $this->type,
            'second' => $this->second,
            'status' => $this->status,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        if ($this->from_date) {
            $from_date = Yii::$app->jdf::jalaliToTimestamp($this->from_date, "Y/m/d");
            $query->andFilterWhere(['>=', 'from_date', $from_date]);
        }
        if ($this->to_date) {
            $to_date = Yii::$app->jdf::jalaliToTimestamp($this->to_date, "Y/m/d");
            $query->andFilterWhere(['<=', 'from_date', $to_date]);
        }


        return $dataProvider;
    }

    public function searchReport($params)
    {
        $query = SalaryItemsAddition::find()
            ->select([
                SalaryItemsAddition::tableName() . '.*',
                "SUM(`second`/(8*60)) as total"
            ])
            ->groupBy(['user_id'])
            ->confirm();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['total' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['total'] = [
            'asc' => ['total' => SORT_ASC],
            'desc' => ['total' => SORT_DESC],
        ];

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
            'kind' => $this->kind,
            'type' => $this->type,
            'second' => $this->second,
            'status' => $this->status,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        if ($this->from_date) {
            $from_date = Yii::$app->jdf::jalaliToTimestamp($this->from_date, "Y/m/d");
            $query->andFilterWhere(['>=', 'from_date', $from_date]);
        }
        if ($this->to_date) {
            $to_date = Yii::$app->jdf::jalaliToTimestamp($this->to_date, "Y/m/d");
            $query->andFilterWhere(['<=', 'from_date', $to_date]);
        }


        return $dataProvider;
    }
}
