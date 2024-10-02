<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_contract_templates}}".
 *
 * @property int $id
 * @property string|null $title
 * @property int $type
 * @property string|null $description
 * @property string|null $signatures
 * @property int $status
 * @property array $clauses
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property array $variables
 * @property string|null $typeText
 */
class ContractTemplatesBase extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;

    const STATUS_DELETED = 0;

    const TYPE_CONTRACT = 1;
    const TYPE_LETTER = 2;

    const TYPE_CUSTOMER = 3;

	const SCENARIO_CREATE = 'create';

	public $clausesModels;

    public $json_file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_contract_templates}}';
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
			[['title', 'type', 'description'], 'required'],
            [['title', 'description', 'signatures'], 'string'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
			[['clauses', 'variables'], 'safe'],
            ['type', 'in', 'range' => [self::TYPE_CONTRACT, self::TYPE_CUSTOMER, self::TYPE_LETTER]],
            //[['variables'], 'validateVariables'],
            [['json_file'], 'file', 'extensions' => 'json'],
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
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'signatures' => 'امضا کنندگان',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['id', 'title', 'type', 'description', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        return $scenarios;
    }

    public function validateVariables()
    {
        if (!empty($this->variables)) {
            foreach ($this->variables as $key => $variable) {
                if (!$variable) {
                    $this->addError("variables[$key]", Yii::t('app', 'Variable name is required'));
                }
            }
        }
    }

    public function beforeValidate()
    {
        $this->description = self::fixContnent($this->description);
        $this->signatures = self::fixContnent($this->signatures);
        return parent::beforeValidate();
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

    /**
     * {@inheritdoc}
     * @return ContractTemplatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ContractTemplatesQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
		if(UserContracts::find()->byContractId($this->id)->limit(1)->exists()){
			return false;
		}
        return true;
    }
    /*
    * حذف منطقی
    */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        if ($this->canDelete() && $this->save()) {
            return true;
        } else {
            return false;
        }
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
			case 'ListContract';
				$_items = ArrayHelper::map(self::find()->andWhere(['type' => self::TYPE_CONTRACT])->all(), 'id', 'title');
				break;
            case 'ListCustomer';
                $_items = ArrayHelper::map(self::find()->andWhere(['type' => self::TYPE_CUSTOMER])->all(), 'id', 'title');
                break;
            case 'ListOfficialLetter';
                $_items = ArrayHelper::map(self::find()->andWhere(['type' => self::TYPE_LETTER])->all(), 'id', 'title');
                break;
			case 'Status';
				$_items = [
					self::STATUS_ACTIVE => Yii::t('app', 'Status Active'),
					self::STATUS_DELETED => Yii::t('app', 'Status Delete'),
				];
				break;
            case 'Type';
                $_items = [
                    self::TYPE_CONTRACT => Yii::t('app', 'Contract'),
                    self::TYPE_CUSTOMER => Yii::t('app', 'Customer'),
                    self::TYPE_LETTER => Yii::t('app', 'Letter'),
                ];
                break;
            case 'TypeText';
                $_items = [
                    self::TYPE_CONTRACT => 'Contract',
                    self::TYPE_CUSTOMER => 'Customer',
                    self::TYPE_LETTER => 'Letter',
                ];
                break;
		}

		if (isset($code)) {
			return $_items[$code] ?? null;
		}
		return $_items ?: null;
	}

    public function getTypeText(): ?string
    {
        return self::itemAlias('TypeText', $this->type) ?: 'Contract';
    }

	public function afterFind()
	{
		foreach ((is_array($this->clauses) ? $this->clauses : []) as $index => $clause) {
			$this->clausesModels[] = new ContractClausesModel($clause);
		}
		parent::afterFind();
	}

	public function beforeSave($insert)
	{
		if(is_array($this->clausesModels)) {
			$this->clauses = $this->clausesModels;
		}
		return parent::beforeSave($insert);
	}

    public static function fixContnent(?string $content): ?string
    {
        return $content ? preg_replace('/(width(\s?)+:(\s?)+([0-9])+(\s?)+px(\;?))/i', '', $content) : $content;
    }
}
