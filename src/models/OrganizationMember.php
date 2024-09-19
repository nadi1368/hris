<?php

namespace hesabro\hris\models;

use backend\models\User;
use common\behaviors\Jsonable;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%employee_user_contracts}}".
 *
 * @property int $id
 * @property int|null $contract_id
 */
class OrganizationMember extends \yii\db\ActiveRecord
{

	/** Additional Data */
	public $headline;
	public $show_internal_number;
	public $show_job_tag;

	/**
	 * Use to build Highcharts.js organization chart
	 * @var array
	 */
	public $children;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%organization_member}}';
	}

	public function behaviors()
	{
		return [
			[
				'class' => TimestampBehavior::class,
			],
			[
				'class' => BlameableBehavior::class,
			],
			'JSON' => [
				'class' => Jsonable::class,
				'jsonAttributes' => [
					'additional_data' => [
						'headline',
						'show_job_tag',
						'show_internal_number',
					]
				]
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'user_id'], 'required'],
			[['name', 'headline'], 'string'],
			[['show_internal_number', 'show_job_tag'], 'boolean'],
			[['parent_id', 'user_id'], 'integer'],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['parent_id'], 'exist', 'targetClass' => self::class, 'targetAttribute' => ['parent_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'ID'),
			'name' => Yii::t('app', 'Title'),
			'user_id' => Yii::t('app', 'Related User'),
			'parent_id' => Yii::t('app', 'Upper Agent'),
			'headline' => Yii::t('app', 'Headline'),
			'show_internal_number' => Yii::t('app', 'Show Internal Number'),
			'show_job_tag' => Yii::t('app', 'Show Job Label'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent()
	{
		return $this->hasOne(self::class, ['id' => 'parent_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreator()
	{
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUpdate()
	{
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}

	/**
	 * {@inheritdoc}
	 * @return OrganizationMemberQuery the active query used by this AR class.
	 */
	public static function find()
	{
		$query = new OrganizationMemberQuery(get_called_class());
		return $query;
	}

	public function canUpdate()
	{
		return true;
	}

	public function canDelete()
	{
		return true;
	}

	/**
	 * Build hierarchy
	 * Use to build Highcharts.js organization chart
	 */
	static function buildHighchartsHierarchy($members, $parentId = null)
	{
		$tree = [];

		foreach ($members as $key => $record) {
			if ($record->parent_id == $parentId) {
				// Remove the current record from the list
				unset($members[$key]);

				// Recursively find children
				$children = self::buildHighchartsHierarchy($members, $record->id);

				// If there are children, add them to the current record
				if ($children) {
					$record->children = $children;
				}

				// Add the current record to the tree
				$tree[] = $record;
			}
		}

		return $tree;
	}

	/**
	 * Get nodes as connected elements arrays
	 * Use to build Highcharts.js organization chart
	 * 
	 * @return array
	 */
	static function buildHighchartsData($tree, $result = [], $parent = null)
	{
		foreach ($tree as $node) {
			if ($parent) {
				$result[] = [$parent->id, $node->id];
			}
			if (!empty($node->children)) {
				$result = self::buildHighchartsData($node->children, $result, $node);
			}
		}
		return $result;
	}

	/**
	 * Get member related job tag
	 * 
	 * @return string|null
	 */
	public function getJobTag(): string|null
	{
		return $this->user?->customer?->getJob();
	}

	/**
	 * Get member related internal number
	 * 
	 * @return InternalNumber|null
	 */
	public function getInternalNumber(): InternalNumber|null
	{
		if (!$this->user_id) return null;
		return InternalNumber::find()->where(['user_id' => $this->user_id])->one();
	}

	/**
	 * Get member full headline including headline itself and internale number
	 * 
	 * @return string|null
	 */
	public function getFullHeadline(): string|null
	{
		if (!$this->headline) return null;
		if ($this->show_internal_number && !$this->getInternalNumber()?->number) return null;
		if ($this->show_job_tag && !$this->getJobTag()) return null;

		return join('<br/>', array_filter([
			nl2br($this->headline),
			($this->show_internal_number ? $this->getInternalNumber()?->number : null),
			($this->show_job_tag ? $this->getJobTag() : null)
		], fn ($item) => $item != null));
	}
}
