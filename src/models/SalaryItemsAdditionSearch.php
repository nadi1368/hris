<?php

namespace hesabro\hris\models;

use backend\models\AuthAssignment;
use backend\models\User;
use hesabro\hris\Module;
use hesabro\hris\traits\ModuleTrait;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\validators\DateValidator;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * SalaryItemsAdditionSearch represents the model behind the search form of `hesabro\hris\models\SalaryItemsAddition`.
 *
 *
 * @property-read Module $module
 */
class SalaryItemsAdditionSearch extends SalaryItemsAddition
{
    use ModuleTrait;
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
            $from_date = Jdf::jalaliToTimestamp($this->from_date,"Y/m/d");
            $query->andFilterWhere(['>=', 'from_date', $from_date]);
        }
        if ($this->to_date) {
            $to_date = Jdf::jalaliToTimestamp($this->to_date,"Y/m/d");
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
            $from_date = Jdf::jalaliToTimestamp($this->from_date, "Y/m/d");
            $query->andFilterWhere(['>=', 'from_date', $from_date]);
        }
        if ($this->to_date) {
            $to_date = Jdf::jalaliToTimestamp($this->to_date, "Y/m/d");
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
            $from_date = Jdf::jalaliToTimestamp($this->from_date, "Y/m/d");
            $query->andFilterWhere(['>=', 'from_date', $from_date]);
        }
        if ($this->to_date) {
            $to_date = Jdf::jalaliToTimestamp($this->to_date, "Y/m/d");
            $query->andFilterWhere(['<=', 'from_date', $to_date]);
        }


        return $dataProvider;
    }

    public function searchReportLeaveLineChart($params)
    {
        $employees = User::find()
            ->select(['id', 'username', 'first_name', 'last_name'])
            ->joinWith('authAssignment')
            ->andWhere([AuthAssignment::tableName() . '.item_name' => $this->module->employeeRole])
            ->all();

        $kindHourly = SalaryItemsAddition::KIND_LEAVE_HOURLY;
        $kindColumn = SalaryItemsAddition::tableName() . '.kind';
        $query = SalaryItemsAddition::find()
            ->select([
                SalaryItemsAddition::tableName() . '.user_id',
                'year' => '`hesabro`.pyear(FROM_UNIXTIME(`from_date`))',
                'month' => '`hesabro`.pmonth(FROM_UNIXTIME(`from_date`))',
                'total' => "SUM(CASE WHEN $kindColumn=$kindHourly THEN (`second`/(8*60)) ELSE ((`to_date`-`from_date`)/(24*60*60)) END)"
            ])
            ->andWhere([$kindColumn => [SalaryItemsAddition::KIND_LEAVE_HOURLY, SalaryItemsAddition::KIND_LEAVE_DAILY]])
            ->andWhere([SalaryItemsAddition::tableName() . '.status' => SalaryItemsAddition::STATUS_CONFIRM])
            ->andWhere(['IN', SalaryItemsAddition::tableName() . '.user_id', array_map(fn(User $user) => $user->id, $employees)])
            ->groupBy([SalaryItemsAddition::tableName() .'.user_id', 'month'])
            ->orderBy([SalaryItemsAddition::tableName() .'.user_id' => SORT_ASC, 'year' => SORT_ASC, 'month' => SORT_ASC]);

        $from = $this->from_date ? Jdf::jalaliToTimestamp($this->from_date,"Y/m/d") : strtotime('-1 year');
        $to = $this->to_date ? Jdf::jalaliToTimestamp($this->to_date,"Y/m/d") : time();

        $query->andFilterWhere(['>=', 'from_date', $from]);
        $query->andFilterWhere(['<=', 'from_date', $to]);

        $items = $query->all();

        $chartData = [
            'names' => [Yii::t('app', 'Month')]
        ];

        foreach ($items as $item) {
            $chartData['names'][$item->user_id] = $item->user->fullName;
        }
        ksort($chartData['names']);

        $filterTimestamp = $from;
        do {
            [$y, $m] = explode('/', Yii::$app->jdf->jdate('Y/m', $filterTimestamp, tr_num: 'en'));
            $key = (int) ($y.$m);
            $chartData[$key] = ["$y/$m"];

            foreach ($chartData['names'] as $userId => $nameName) {
                if ($userId <= 0) {
                    continue;
                }
                $userData = current(array_filter($items, fn($i) => $i->user_id == $userId && $i->year == $y && $i->month == $m));
                $chartData[$key][$userId] = (float) ($userData ? $userData->total : 0);
            }

            $filterTimestamp = Jdf::jalaliToTimestamp(Yii::$app->jdf->add_month($filterTimestamp), 'Y/m/d');
        } while ($filterTimestamp < $to);

        return array_values(array_map(fn(array $data) => array_values($data), $chartData));
    }
}
