<?php

namespace hesabro\hris\models;

use common\components\Jdate;
use common\components\jdf\Jdf;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\RequestLeave;

/**
 * RequestLeaveSearch represents the model behind the search form of `backend\models\RequestLeave`.
 */
class RequestLeaveSearch extends RequestLeave
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'branch_id', 'user_id', 'manager_id', 'type', 'status', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['description', 'from_date', 'range', 'to_date'], 'safe'],
        ];
    }

    public function beforeValidate()
    {
        $date_range = $this->rangeToTimestampRange($this->range, "Y/m/d H:i:s", 1, " - ", 00, 00, 00);
        $this->from_date = $date_range['start'];
        $this->to_date = $date_range['end'];
        return parent::beforeValidate();
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
        $query = RequestLeave::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id'         => $this->id,
            'branch_id'  => $this->branch_id,
            'user_id'    => $this->user_id,
            'manager_id' => $this->manager_id,
            'type'       => $this->type,
//            'from_date'  => $this->from_date,
//            'to_date'    => $this->to_date,
            'status'     => $this->status,
            'creator_id' => $this->creator_id,
            'update_id'  => $this->update_id,
            'created'    => $this->created,
            'changed'    => $this->changed,
        ]);

        if (isset($this->from_date) && !empty($this->from_date)) {
            $query->andFilterWhere(['>=', 'from_date', $this->from_date]);
        }

        if (isset($this->to_date) && !empty($this->to_date)) {
            $query->andFilterWhere(['<=', 'to_date', $this->to_date]);
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
    public function searchManage($params)
    {
        $query = RequestLeave::find()->manage(Yii::$app->user->id);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id'         => $this->id,
            'branch_id'  => $this->branch_id,
            'user_id'    => $this->user_id,
            'manager_id' => $this->manager_id,
            'type'       => $this->type,
//            'from_date'  => $this->from_date,
//            'to_date'    => $this->to_date,
            'status'     => $this->status,
            'creator_id' => $this->creator_id,
            'update_id'  => $this->update_id,
            'created'    => $this->created,
            'changed'    => $this->changed,
        ]);

        if (isset($this->from_date) && !empty($this->from_date)) {
            $query->andFilterWhere(['>=', 'from_date', $this->from_date]);
        }

        if (isset($this->to_date) && !empty($this->to_date)) {
            $query->andFilterWhere(['<=', 'to_date', $this->to_date]);
        }

        $query->andFilterWhere(['like', 'description', $this->description]);
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchMy($params)
    {
        $query = RequestLeave::find()->my(Yii::$app->user->id);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id'     => $this->id,
            'type'   => $this->type,
//            'from_date' => $this->from_date,
//            'to_date'   => $this->to_date,
            'status' => $this->status
        ]);

        if (isset($this->from_date) && !empty($this->from_date)) {
            $query->andFilterWhere(['>=', 'from_date', $this->from_date]);
        }

        if (isset($this->to_date) && !empty($this->to_date)) {
            $query->andFilterWhere(['<=', 'to_date', $this->to_date]);
        }


        return $dataProvider;
    }
}
