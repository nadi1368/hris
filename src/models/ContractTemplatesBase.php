<?php

namespace hesabro\hris\models;

use hesabro\automation\models\AuLetter;
use hesabro\helpers\behaviors\JsonAdditional;
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
 * @property bool $isLetter
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

    // Additional Data

    public $au_letter_type = null;

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
            'JsonAdditional' => [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'notSaveNull' => true,
                'AdditionalDataProperty' => [
                    'au_letter_type' => 'NullInteger'
                ],
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
            [['au_letter_type'], 'in', 'range' => array_keys(AuLetter::itemAlias('Type'))],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'type' => Module::t('module', 'Type'),
            'description' => Module::t('module', 'Description'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'updated_at' => Module::t('module', 'Updated At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_by' => Module::t('module', 'Updated By'),
            'signatures' => 'امضا کنندگان',
            'au_letter_type' => Module::t('module', 'Letter Type')
        ];
    }

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'au_letter_type' => implode(' ', [
                Module::t('module', 'Create Letter In Automation System'),
                '('.Module::t('module', 'Optional').')',
            ])
        ]);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['id', 'title', 'type', 'description', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'au_letter_type'];

        return $scenarios;
    }

    public function validateVariables()
    {
        if (!empty($this->variables)) {
            foreach ($this->variables as $key => $variable) {
                if (!$variable) {
                    $this->addError("variables[$key]", Module::t('module', 'Variable name is required'));
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
					self::STATUS_ACTIVE => Module::t('module', 'Status Active'),
					self::STATUS_DELETED => Module::t('module', 'Status Delete'),
				];
				break;
            case 'Type';
                $_items = [
                    self::TYPE_CONTRACT => Module::t('module', 'Contract'),
                    self::TYPE_CUSTOMER => Module::t('module', 'Customer'),
                    self::TYPE_LETTER => Module::t('module', 'Letter'),
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

    public function getIsLetter(): bool
    {
        return $this->type === self::TYPE_LETTER;
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
