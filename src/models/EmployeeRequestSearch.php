<?php

namespace hesabro\hris\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class EmployeeRequestSearch extends EmployeeRequest
{
    public function rules(): array
    {
        return [
            [['user_id', 'branch_id', 'type', 'status'], 'integer'],
            ['type', 'in', 'range' => [EmployeeRequest::TYPE_LETTER]],
            ['status', 'in', 'range' => [EmployeeRequest::STATUS_PENDING, EmployeeRequest::STATUS_REJECT, EmployeeRequest::STATUS_ACCEPT]],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = EmployeeRequest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'branch_id' => $this->branch_id,
            'type' => $this->type,
            'status' => $this->status
        ]);

        return $dataProvider;
    }

    public function searchUser($params, EmployeeBranchUser $employee): ActiveDataProvider
    {
        $query = EmployeeRequest::find()->byEmployee($employee);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'branch_id' => $this->branch_id,
            'type' => $this->type,
            'status' => $this->status
        ]);

        return $dataProvider;
    }
}
