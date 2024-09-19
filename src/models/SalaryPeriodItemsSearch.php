<?php

namespace hesabro\hris\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SalaryPeriodItemsSearch represents the model behind the search form of `hesabro\hris\models\SalaryPeriodItems`.
 */
class SalaryPeriodItemsSearch extends SalaryPeriodItems
{
    public $check_advance_money = false;
    public $check_document = false;

    public $kind;
    public $show_iban = false;
    public $showAccounting = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'period_id', 'hours_of_work', 'count_of_children', 'hours_of_overtime', 'can_payment', 'creator_id', 'update_id', 'created', 'changed', 'kind'], 'integer'],
            [['basic_salary', 'cost_of_house', 'cost_of_food', 'cost_of_children', 'cost_of_year', 'rate_of_year', 'commission', 'total_salary', 'advance_money', 'payment_salary'], 'number'],
            [['check_advance_money', 'check_document', 'show_iban', 'showAccounting'], 'boolean'],
            [['user_id'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        $attributeLabels['check_advance_money'] = 'مغایرت مساعده ثبت شده با دریافتی';
        $attributeLabels['check_document'] = 'مغایرت سند';
        $attributeLabels['kind'] = 'نوع';
        $attributeLabels['show_iban'] = 'نمایش اطلاعات کارمندی';
        $attributeLabels['showAccounting'] = 'نمایش اطلاعات ویژه حسابداری';

        return $attributeLabels;
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
     * @param $params
     * @param $periodId
     * @return ActiveDataProvider
     */
    public function search($params, $periodId)
    {
        $query = SalaryPeriodItems::find()->andWhere(['period_id' => $periodId]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['count_point'] = [
            'asc' => ['JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.count_point")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.count_point")' => SORT_DESC],
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
            'period_id' => $this->period_id,
            'user_id' => $this->user_id,
            'hours_of_work' => $this->hours_of_work,
            'basic_salary' => $this->basic_salary,
            'cost_of_house' => $this->cost_of_house,
            'cost_of_food' => $this->cost_of_food,
            'cost_of_children' => $this->cost_of_children,
            'count_of_children' => $this->count_of_children,
            'cost_of_year' => $this->cost_of_year,
            'rate_of_year' => $this->rate_of_year,
            'hours_of_overtime' => $this->hours_of_overtime,
            'commission' => $this->commission,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
            'total_salary' => $this->total_salary,
            'advance_money' => $this->advance_money,
            'payment_salary' => $this->payment_salary,
            'can_payment' => $this->can_payment,
        ]);

        if ($this->check_advance_money) {
            $query->varianceAdvanceMoney();
        }
        if ($this->check_document) {
            $query->andWhere('(total_salary+insurance_owner)<>(insurance+insurance_owner+tax+payment_salary)');
        }
        return $dataProvider;
    }

    public function searchByUser($params, $user_id)
    {
        $query = SalaryPeriodItems::find()->andWhere(['user_id' => $user_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['count_point'] = [
            'asc' => ['JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.count_point")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.count_point")' => SORT_DESC],
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
            'period_id' => $this->period_id,
            'user_id' => $this->user_id,
            'hours_of_work' => $this->hours_of_work,
            'basic_salary' => $this->basic_salary,
            'cost_of_house' => $this->cost_of_house,
            'cost_of_food' => $this->cost_of_food,
            'cost_of_children' => $this->cost_of_children,
            'count_of_children' => $this->count_of_children,
            'cost_of_year' => $this->cost_of_year,
            'rate_of_year' => $this->rate_of_year,
            'hours_of_overtime' => $this->hours_of_overtime,
            'commission' => $this->commission,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
            'total_salary' => $this->total_salary,
            'advance_money' => $this->advance_money,
            'payment_salary' => $this->payment_salary,
            'can_payment' => $this->can_payment,
        ]);

        if ($this->check_advance_money) {
            $query->varianceAdvanceMoney();
        }
        if ($this->check_document) {
            $query->andWhere('(total_salary+insurance_owner)<>(insurance+insurance_owner+tax+payment_salary)');
        }
        return $dataProvider;
    }


    /**
     * @param $params
     * @param $periodId
     * @return ActiveDataProvider
     */
    public function searchUser($params)
    {
        $query = SalaryPeriodItems::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['count_point'] = [
            'asc' => ['JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.count_point")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.count_point")' => SORT_DESC],
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
            'period_id' => $this->period_id,
            'user_id' => $this->user_id,
            'hours_of_work' => $this->hours_of_work,
            'basic_salary' => $this->basic_salary,
            'cost_of_house' => $this->cost_of_house,
            'cost_of_food' => $this->cost_of_food,
            'cost_of_children' => $this->cost_of_children,
            'count_of_children' => $this->count_of_children,
            'cost_of_year' => $this->cost_of_year,
            'rate_of_year' => $this->rate_of_year,
            'hours_of_overtime' => $this->hours_of_overtime,
            'commission' => $this->commission,
            'creator_id' => $this->creator_id,
            'update_id' => $this->update_id,
            'created' => $this->created,
            'changed' => $this->changed,
            'total_salary' => $this->total_salary,
            'advance_money' => $this->advance_money,
            'payment_salary' => $this->payment_salary,
            'can_payment' => $this->can_payment,
        ]);

        if ($this->check_advance_money) {
            $query->varianceAdvanceMoney();
        }

        if (isset($this->kind)) {
            $query->joinWith(['period']);
            if ($this->kind == SalaryPeriod::KIND_SALARY) {
                $query->andWhere(['OR',
                    ['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_SALARY],
                    'JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind") IS NULL'
                ]);
            } else {
                $query->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => $this->kind]);
            }
        }
        return $dataProvider;
    }

    public function totalWorkByUser(int $userId): int|null
    {
        return SalaryPeriodItems::find()
            ->from(SalaryPeriodItems::tableName() . ' AS t1')
            ->leftJoin(SalaryPeriod::tableName() . ' AS t2', 't2.id = t1.period_id')
            ->andWhere(['user_id' => $userId])
            ->andWhere('JSON_EXTRACT(t2.`additional_data`, "$.kind") = ' . SalaryPeriod::KIND_SALARY)
            ->sum('hours_of_work');
    }
}
