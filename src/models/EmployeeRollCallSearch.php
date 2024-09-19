<?php

namespace hesabro\hris\models;

use common\validators\DateValidator;
use sadi01\bidashboard\models\ReportWidget;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EmployeeRollCallSearch represents the model behind the search form of `hesabro\hris\models\EmployeeRollCall`.
 */
class EmployeeRollCallSearch extends EmployeeRollCall
{

    public $fromDate;
    public $toDate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 't_id'], 'integer'],
            [['date', 'status'], 'safe'],
            [['total', 'shift', 'over_time', 'low_time', 'in_1', 'out_1', 'in_2', 'out_2', 'in_3', 'out_3'], 'number'],
            [['fromDate', 'toDate'], DateValidator::class],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
        ]);
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
        $query = EmployeeRollCall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['date' => SORT_DESC]]
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
            'total' => $this->total,
            'shift' => $this->shift,
            'over_time' => $this->over_time,
            'low_time' => $this->low_time,
            'in_1' => $this->in_1,
            'out_1' => $this->out_1,
            'in_2' => $this->in_2,
            'out_2' => $this->out_2,
            'in_3' => $this->in_3,
            'out_3' => $this->out_3,
            't_id' => $this->t_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        $query->andFilterWhere(['>=', 'date', $this->fromDate]);
        $query->andFilterWhere(['<=', 'date', $this->toDate]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param $userId
     * @return ActiveDataProvider
     */
    public function searchUser($params, $userId)
    {
        $query = EmployeeRollCall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
            'pagination' => ['pageSize' => 31],
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
            'user_id' => $userId,
            'total' => $this->total,
            'shift' => $this->shift,
            'over_time' => $this->over_time,
            'low_time' => $this->low_time,
            'in_1' => $this->in_1,
            'out_1' => $this->out_1,
            'in_2' => $this->in_2,
            'out_2' => $this->out_2,
            'in_3' => $this->in_3,
            'out_3' => $this->out_3,
            't_id' => $this->t_id,
        ]);

        $query
            ->andFilterWhere(['like', 'status', $this->status]);

        $query->andFilterWhere(['>=', 'date', $this->fromDate]);
        $query->andFilterWhere(['<=', 'date', $this->toDate]);

        return $dataProvider;
    }

    public function searchBiModule(array $params, int $rangeType, int $startRange, int $endRange)
    {
        $this->load($params, '');

        $query = EmployeeRollCall::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total' => $this->total,
            'shift' => $this->shift,
            'over_time' => $this->over_time,
            'low_time' => $this->low_time,
            'in_1' => $this->in_1,
            'out_1' => $this->out_1,
            'in_2' => $this->in_2,
            'out_2' => $this->out_2,
            'in_3' => $this->in_3,
            'out_3' => $this->out_3,
            't_id' => $this->t_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        $query->andFilterWhere(['>=', 'date', $this->fromDate]);
        $query->andFilterWhere(['<=', 'date', $this->toDate]);


        if ($rangeType == ReportWidget::RANGE_TYPE_MONTHLY) {
            $query->select([
                'total_over_time' => 'SUM(over_time)',
                'total_low_time' => 'SUM(low_time)',
                'total_mission_time' => 'SUM(mission_time)',
                'total_leave_time' => 'SUM(leave_time)',
                'year' => 'pyear(gdatestr(date))',
                'month' => 'pmonth(gdatestr(date))',
                'month_name' => 'pmonthname(gdatestr(date))',
            ]);
            $query
                ->groupBy('pyear(gdatestr(date)), pmonth(gdatestr(date))')
                ->orderBy('UNIX_TIMESTAMP(gdatestr(date))');
        } elseif ($rangeType == ReportWidget::RANGE_TYPE_DAILY) {
            $query->select([
                'total_over_time' => 'SUM(over_time)',
                'total_low_time' => 'SUM(low_time)',
                'total_mission_time' => 'SUM(mission_time)',
                'total_leave_time' => 'SUM(leave_time)',
                'year' => 'pyear(gdatestr(date))',
                'month' => 'pmonth(gdatestr(date))',
                'month_name' => 'pmonthname(gdatestr(date))',
            ]);
            $query
                ->groupBy('pday(gdate(date)), pmonth(gdatestr(date)), pyear(gdatestr(date))')
                ->orderBy('UNIX_TIMESTAMP(gdatestr(date))');
        }

        $query->andFilterWhere(['between', 'UNIX_TIMESTAMP(gdatestr(date))', $startRange, $endRange]);

        return $dataProvider;
    }
}
