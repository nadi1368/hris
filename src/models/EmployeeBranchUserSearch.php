<?php

namespace hesabro\hris\models;

use common\components\Helper;
use common\validators\DateValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EmployeeBranchUserSearch represents the model behind the search form of `hesabro\hris\models\EmployeeBranchUser`.
 */
class EmployeeBranchUserSearch extends EmployeeBranchUser
{
    public $show_on_salary_list;
    public $show_on_end_work = false;
    public $set_iban;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'user_id', 'show_on_salary_list', 'set_iban', 'status', 'roll_call_id'], 'integer'],
            [['show_on_end_work'], 'boolean'],
            [['end_work'], DateValidator::class],
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels ['show_on_salary_list'] = 'نمایش در لیست حقوق';
        $labels ['show_on_end_work'] = 'نمایش ترک کاری ها';
        $labels ['set_iban'] = 'ست شدن حساب بانکی';
        $labels ['roll_call_id'] = 'ست شدن تفضیل حضور و غیاب';

        return $labels;
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
        $query = EmployeeBranchUser::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['insurance_code'] = [
            'asc' => ['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.insurance_code")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.insurance_code")' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['roll_call_id'] = [
            'asc' => ['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.roll_call_id")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.roll_call_id")' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['account_id'] = [
            'asc' => ['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.account_id")' => SORT_ASC],
            'desc' => ['JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.account_id")' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ((int)$this->show_on_salary_list == Helper::YES) {
            $query->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")=""')
                ->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.disable_show_on_salary_list") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.disable_show_on_salary_list")=false');
        }

        if ((int)$this->show_on_salary_list == Helper::NO) {
            $query->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")<>"" OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.disable_show_on_salary_list")=true');
        }

        if ((int)$this->roll_call_id == Helper::YES) {
            $query->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.roll_call_id") > 0');
        }

        if ((int)$this->roll_call_id == Helper::NO) {
            $query->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.roll_call_id") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.roll_call_id")=""');
        }

        if ((int)$this->set_iban == Helper::YES) {
            $query->andWhere('shaba IS NOT NULL');
        }

        if ((int)$this->set_iban == Helper::NO) {
            $query->andWhere('shaba IS NULL');
        }

        if ((int)$this->end_work > 0) {
            $query->andWhere(['AND',['<=', 'JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")', $this->end_work], ['<>','JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")','']]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'branch_id' => $this->branch_id,
            'user_id' => $this->user_id
        ]);

        return $dataProvider;
    }


    public function searchSalary($params,$userIds)
    {
        $query = EmployeeBranchUser::find()
            ->andWhere(['NOT IN','user_id', $userIds])
            ->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")=""');

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
            'branch_id' => $this->branch_id,
            'user_id' => $this->user_id
        ]);

        return $dataProvider;
    }

    public function searchReward($params,$userIds)
    {
        $query = EmployeeBranchUser::find()
            ->andWhere(['NOT IN','user_id', $userIds]);

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

        if ((int)$this->end_work > 0) {
            $query->andWhere(['AND',['<=', 'JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")', $this->end_work], ['<>','JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")','']]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'branch_id' => $this->branch_id,
            'user_id' => $this->user_id
        ]);

        return $dataProvider;
    }
}
