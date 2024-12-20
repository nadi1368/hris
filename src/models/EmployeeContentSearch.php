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
                    EmployeeContent::TYPE_SOFTWARE, EmployeeContent::TYPE_BUSINESS,
                    EmployeeContent::TYPE_JOB_DESCRIPTION, EmployeeContent::TYPE_ANNOUNCEMENT
                ]
            ],
            [['ignore_client'], 'boolean'],
            [['title'], 'safe'],
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
            'sort' => [
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

        $query->andFilterWhere(['like', 'title', $this->title]);


        if ($this->scattered_search_query) {
            $query->byScatteredSearch($this->scattered_search_query);
        }

        return $dataProvider;
    }

    public function searchForEmployee($params, $pagination = ['pageSize' => 20])
    {
        $userId = Yii::$app->user->identity->id;
        $employee = EmployeeBranchUser::find()->where(['user_id' => $userId])->with(['user'])->one();

        $query = EmployeeContent::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort' => [
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

        $query->andFilterWhere(['like', 'title', $this->title]);

        $query->byCustomUserId($userId);

        $query->byCustomJobTags($employee->job_code);

        if ($this->scattered_search_query) {
            $query->byScatteredSearch($this->scattered_search_query);
        }

        return $dataProvider;
    }
}
