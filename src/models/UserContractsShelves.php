<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_user_contracts_shelves}}".
 *
 * @property int $id
 * @property int $title
 * @property int $capacity
 * @property int $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserContractsShelves extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

	const SCENARIO_CREATE = 'create';

	public $err_msg;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_user_contracts_shelves}}';
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
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'capacity'], 'required'],
            [['capacity', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title'], 'string', 'max' => 255],
            [['title'], 'unique'],
			[['capacity'], 'integer', 'min' => 1],
			[['capacity'], 'validateCapacity'],
			[['title'], 'validateTitle'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'capacity' => Yii::t('app', 'Capacity'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'active_contracts_count' => Yii::t('app', 'Active Contracts Count'),
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['id', 'title', 'capacity', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', ];

        return $scenarios;
    }


	public function validateCapacity()
	{
		if ($this->capacity < $this->getActiveContracts()->count()) {
			$this->addError('capacity', 'ظرفیت نمیتواند کمتر از تعداد قراردادهای موجود در جایگاه باشد.');
		}
	}

	public function validateTitle()
	{
		if ($this->getActiveContracts()->limit(1)->one() && ($this->title != $this->getOldAttribute('title'))) {
			$this->addError('title', 'امکان تغییر نام جایگاه وجود ندارد.');
		}
	}

    /**
    * @return \yii\db\ActiveQuery
    */
	public function getCreator()
	{
		return $this->hasOne(Module::getInstance()->user, ['id' => 'created_by']);
	}

	/**
	* @return \yii\db\ActiveQuery
	*/
	public function getUpdate()
	{
		return $this->hasOne(Module::getInstance()->user, ['id' => 'updated_by']);
	}

	public function getActiveContracts()
	{
		return $this->hasMany(UserContracts::class, ['shelf_id' => 'id'])->andOnCondition([UserContracts::tableName() . '.status' => UserContracts::STATUS_CONFIRM]);
	}

    /**
     * {@inheritdoc}
     * @return UserContractsShelvesQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new UserContractsShelvesQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
		if($this->getActiveContracts()->count() != 0){
			$this->err_msg = 'جایگاه دارای قرارداد فعال هست و امکان حذف وجود ندارد.';
			return false;
		}
        return true;
    }
    /*
    * حذف منطقی
    */
    public function softDelete()
    {
		if($this->canDelete()){
			$this->status = self::STATUS_DELETED;
			if ($this->save()) {
				return true;
			}
		}
		return false;
    }

    /*
    * فعال کردن
    */
    public function restore()
    {
        $this->status = self::STATUS_ACTIVE;
        if ($this->save()) {
            return true;
        } else {
            return false;
        }
    }

	public static function itemAlias($type, $code = null)
	{
		$_items = [];
		switch ($type){
			case 'List';
				$_items = ArrayHelper::map(self::find()->all(), 'id', 'title');
				break;
			case 'ListEmpty';
				$_items = ArrayHelper::map(self::find()->empty()->all(), 'id', 'title');
				break;
			case 'Status';
				$_items = [
					self::STATUS_ACTIVE => Yii::t('app', 'Status Active'),
					self::STATUS_DELETED => Yii::t('app', 'Status Delete'),
				];
				break;
		}

		if (isset($code)) {
			return $_items[$code] ?? null;
		}
		return $_items ?: null;
	}

}
