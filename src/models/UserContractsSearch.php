<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserContractsSearch represents the model behind the search form of `hesabro\hris\models\UserContracts`.
 */
class UserContractsSearch extends UserContracts
{

	const STATUS_EXPIRING_1_MONTH = 1;
	const STATUS_EXPIRING_1_WEEK = 2;
	const STATUS_EXPIRED_CONTRACT = 3;

	public $fromStartDate;
	public $toStartDate;
	public $fromEndDate;
	public $toEndDate;

	public $contract_status;


	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['id', 'contract_id', 'user_id', 'month', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'contract_status', 'shelf_id'], 'integer'],
			[['start_date', 'end_date', 'variables'], 'safe'],
			[['fromStartDate', 'toStartDate', 'fromEndDate', 'toEndDate'], 'string'],
		];
	}

	public function attributeLabels()
	{
		return parent::attributeLabels() + [
				'contract_status' => 'وضعیت انقضا',
				'fromStartDate' => 'از تاریخ شروع',
				'toStartDate' => 'تا تاریخ شروع',
				'fromEndDate' => 'از تاریخ پایان',
				'toEndDate' => 'تا تاریخ پایان',
			];
	}

	public static function itemAlias($type, $code = null)
	{
		$_items = [];
		switch ($type) {
			case 'contract_status';
				$_items = [
					self::STATUS_EXPIRING_1_MONTH => 'انقضا تا یک ماه آینده',
					self::STATUS_EXPIRING_1_WEEK => 'انقضا تا یک هفته آینده',
					self::STATUS_EXPIRED_CONTRACT => 'منقضی شده',
				];
				break;
		}

		if (isset($code)) {
			return $_items[$code] ?? null;
		}
		return $_items ?: null;
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
		$query = UserContracts::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
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
			'contract_id' => $this->contract_id,
			'user_id' => $this->user_id,
			'month' => $this->month,
			'status' => $this->status,
			'shelf_id' => $this->shelf_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'created_by' => $this->created_by,
			'updated_by' => $this->updated_by,
		]);

		$query->andFilterWhere(['>=', 'start_date', $this->fromStartDate])
			->andFilterWhere(['<=', 'start_date', $this->toStartDate])
			->andFilterWhere(['<=', 'end_date', $this->fromEndDate])
			->andFilterWhere(['<=', 'end_date', $this->toEndDate]);

		if ($this->contract_status) {
			if ($this->contract_status == self::STATUS_EXPIRED_CONTRACT) {
				$query->andWhere(['<=', 'end_date', Yii::$app->jdate->date('Y/m/d')]);
			}
			if ($this->contract_status == self::STATUS_EXPIRING_1_MONTH) {
				$query->andWhere(['>=', 'end_date', Yii::$app->jdate->date('Y/m/d')]);
				$query->andWhere(['<=', 'end_date', Yii::$app->jdate->date('Y/m/d', strtotime('+1 month'))]);
			}
			if ($this->contract_status == self::STATUS_EXPIRING_1_WEEK) {
				$query->andWhere(['>=', 'end_date', Yii::$app->jdate->date('Y/m/d')]);
				$query->andWhere(['<=', 'end_date', Yii::$app->jdate->date('Y/m/d', strtotime('+1 week'))]);
			}
		}

		return $dataProvider;
	}
}
