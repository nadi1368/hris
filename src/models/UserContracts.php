<?php

namespace hesabro\hris\models;

use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\helpers\behaviors\StatusActiveBehavior;
use hesabro\helpers\validators\DateValidator;
use hesabro\hris\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "{{%employee_user_contracts}}".
 *
 * @property int $id
 * @property int|null $contract_id
 * @property int|null $branch_id
 * @property int|null $user_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $month
 * @property string $variables
 * @property int $shelf_id
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property array $additional_data
 * @property array $contract_clauses
 * @property UserContractsShelves $shelf
 * @property ContractTemplates $contract
 * @property object $user
 * @property ContractClausesModel[] $clausesModels
 * @property string $contractTitle
 * @property string $contractDescription
 * @property string $contractSignatures
 * @property ContractClausesModel[] $contractClauses
 * @property EmployeeBranch $branch
 */
class UserContracts extends \yii\db\ActiveRecord
{
	const STATUS_ACTIVE = 1;
	const STATUS_CONFIRM = 2;
	const STATUS_DELETED = 0;

	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_PRE_CREATE = 'pre_create';
	const SCENARIO_CHANGE_SHELF = 'change-shelf';

	public $err_msg = '';

	public $clausesModels;

	/** Additional Data */
	public $contract_title;
	public $contract_description;
	public $contract_signatures;
	public $daily_salary;
	public $right_to_housing;
	public $right_to_food;
	public $right_to_child = '0';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return '{{%employee_user_contracts}}';
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
			[
				'class' => StatusActiveBehavior::class,
			],
			[
				'class' => JsonAdditional::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'contract_title' => 'String',
                    'contract_description' => 'String',
                    'contract_signatures' => 'String',
                    'daily_salary' => 'String',
                    'right_to_housing' => 'String',
                    'right_to_food' => 'String',
                    'right_to_child' => 'String',
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
			[['contract_id'], 'required', 'on' => self::SCENARIO_PRE_CREATE],
			[['shelf_id'], 'required', 'on' => self::SCENARIO_CHANGE_SHELF],
			[['start_date', 'end_date', 'month'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
			[['contract_id', 'user_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'branch_id', 'shelf_id'], 'integer'],
			[['month'], 'number'],
			[['daily_salary', 'right_to_housing', 'right_to_food', 'right_to_child'], 'string'],
			[['variables'], 'required'],
			[['variables'], 'safe'],
			[['start_date', 'end_date'], 'string', 'max' => 255],
			[['start_date', 'end_date'], DateValidator::class],
			[['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractTemplates::class, 'targetAttribute' => ['contract_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['user_id' => 'id']],
			[['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeBranch::class, 'targetAttribute' => ['branch_id' => 'id']],
			[['start_date'], 'validateDate'],
			[['variables'], 'validateVariables'],
			[['additional_data', 'contract_clauses'], 'safe'],
			[['shelf_id'], 'validateShelf'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => Module::t('module', 'ID'),
			'contract_id' => Module::t('module', 'Contract Templates'),
			'branch_id' => Module::t('module', 'Employee Branch'),
			'user_id' => Module::t('module', 'User ID'),
			'start_date' => Module::t('module', 'Start Contract'),
			'end_date' => Module::t('module', 'End Contract'),
			'month' => Module::t('module', 'Number Of Months'),
			'variables' => Module::t('module', 'Variables'),
			'status' => Module::t('module', 'Status'),
			'created_at' => Module::t('module', 'Created At'),
			'updated_at' => Module::t('module', 'Updated At'),
			'created_by' => Module::t('module', 'Created By'),
			'updated_by' => Module::t('module', 'Updated By'),
			'shelf_id' => Module::t('module', 'Position'),
			'daily_salary' => 'حقوق پایه روزانه',
			'right_to_housing' => 'حق مسکن',
			'right_to_food' => 'حق خواربار',
			'right_to_child' => 'حق اولاد',
		];
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();

		$scenarios[self::SCENARIO_CREATE] = ['start_date', 'end_date', 'month', 'variables', 'daily_salary', 'right_to_housing', 'right_to_food', 'right_to_child', 'shelf_id'];
		$scenarios[self::SCENARIO_UPDATE] = ['start_date', 'end_date', 'month', 'variables', 'daily_salary', 'right_to_housing', 'right_to_food', 'right_to_child', 'shelf_id'];
		$scenarios[self::SCENARIO_PRE_CREATE] = ['contract_id'];
		$scenarios[self::SCENARIO_CHANGE_SHELF] = ['shelf_id'];

		return $scenarios;
	}

	public function validateShelf()
	{
		if ($this->shelf_id != $this->getOldAttribute('shelf_id') && $this->shelf->getActiveContracts()->count() >= $this->shelf->capacity) {
			$this->addError('shelf_id', "ظرفیت جایگاه '" . $this->shelf->title . "' تکمیل شده است.");
		}
	}

	public function checkVariables()
	{
        if($this->status == self::STATUS_ACTIVE) {
            foreach ($this->contract->variables as $variable => $variableTitle) {
                if (!isset($this->variables[$variable]) || $this->variables[$variable] === null) {
                    throw new ForbiddenHttpException('تمام متغیر های قرارداد باید تکمیل شوند');
                }
            }
        }
	}

	public function validateVariables()
	{
		foreach ($this->contract->variables as $variable => $variableTitle) {
			if (!isset($this->variables[$variable])) {
				$this->addError('variables', 'تمام متغیر ها باید تکمیل شوند.');
			}
		}
	}

	public function validateDate($attribute, $params)
	{
		if ($this->isNewRecord || ($this->start_date != $this->getOldAttribute('start_date') || $this->end_date != $this->getOldAttribute('end_date'))) {
			$query = UserContracts::find()
				->byBranchId($this->branch_id)
				->byUserId($this->user_id)
				->byContractId($this->contract_id)
				->limit(1);

			$query->andWhere([
				'OR',
				['AND', ['<=', 'start_date', $this->start_date], ['>=', 'end_date', $this->start_date]],
				['AND', ['<=', 'start_date', $this->end_date], ['>=', 'end_date', $this->end_date]],
				['AND', ['>=', 'start_date', $this->start_date], ['<=', 'end_date', $this->end_date]]
			]);

			if (!$this->isNewRecord && $this->id) {
				$query->andWhere(['<>', 'id', $this->id]);
			}

			if ($query->exists()) {
				$this->addError($attribute, 'تاریخ شروع و پایان قرارداد با قرارداد های قبلی تداخل دارد و یا بعد از این تاریخ قرارداد ایجاد شده است.');
			}
		}

		if ($this->end_date <= $this->start_date) {
			$this->addError('end_date', 'تاریخ پایان قرارداد باید بعد از تاریخ شروع باشد.');
		}
	}

	public static function changeVariables($text, $variables, $bold = false)
	{
		$placeholders = [];
		foreach ((array)$variables as $name => $value) {
			if ($bold) {
				$placeholders['{' . $name . '}'] = '<b>' . $value . '</b>';
			} else {
				$placeholders['{' . $name . '}'] = $value;
			}
		}

		return ($placeholders === []) ? $text : strtr($text, $placeholders);
	}

	public function getRightToChild()
	{
		if ($this->right_to_child && \Yii::$app->phpNewVer->strReplace(',', '', $this->right_to_child) > 0) {
			return 'حق اولاد: ' . $this->right_to_child;
		}
		return '';
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContract()
	{
		return $this->hasOne(ContractTemplates::class, ['id' => 'contract_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(Module::getInstance()->user, ['id' => 'user_id']);
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

	public function getShelf()
	{
		return $this->hasOne(UserContractsShelves::class, ['id' => 'shelf_id']);
	}

    public function getBranch()
    {
        return $this->hasOne(EmployeeBranch::class, ['id' => 'branch_id']);
    }

	/**
	 * {@inheritdoc}
	 * @return UserContractsQuery the active query used by this AR class.
	 */
	public static function find()
	{
		$query = new UserContractsQuery(get_called_class());
		return $query->active();
	}

	public function canUpdate()
	{
		return $this->status == self::STATUS_ACTIVE && EmployeeBranchUser::find()->andWhere(['branch_id' => $this->branch_id, 'user_id' => $this->user_id])->one();
	}

	public function canDelete()
	{
		if ($this->status != self::STATUS_ACTIVE) {
			$this->err_msg = 'به دلیل وضعیت قرارداد امکان حذف وجود ندارد.';
			return false;
		}
		return true;
	}

	public function canConfirm()
	{
		return $this->status == self::STATUS_ACTIVE;
	}

	public function canUnConfirm()
	{
		return $this->status == self::STATUS_CONFIRM;
	}

    public function canExtending()
    {
        return $this->status == self::STATUS_CONFIRM
            && !self::find()
                ->byUserId($this->user_id)
                ->byContractId($this->contract_id)
                ->byBranchId($this->branch_id)
                ->andWhere(['>', 'start_date', $this->end_date])
                ->limit(1)->exists();
    }

	public function getContractTitle()
	{
		return $this->status == self::STATUS_CONFIRM ? $this->contract_title : $this->contract->title;
	}

	public function getContractDescription()
	{
		return $this->status == self::STATUS_CONFIRM ? $this->contract_description : $this->contract->description;
	}

	public function getContractSignatures()
	{
		return $this->status == self::STATUS_CONFIRM ? $this->contract_signatures : $this->contract->signatures;
	}

	public function getContractClauses()
	{
		return $this->status == self::STATUS_CONFIRM ? $this->clausesModels : $this->contract->clausesModels;
	}

	public function confirm()
	{
		$this->contract_title = $this->contract->title;
		$this->contract_description = $this->contract->description;
		$this->contract_signatures = $this->contract->signatures;
		$this->contract_clauses = $this->contract->clausesModels;

		$this->status = self::STATUS_CONFIRM;

		return $this->save(false);
	}

	public function unConfirm()
	{
		$this->status = self::STATUS_ACTIVE;
		return $this->save(false);
	}

	public function setVariables()
	{
		$variables = $this->variables;
		$staticVariables = self::itemAlias('ContractStaticVariables');
		foreach ($staticVariables as $variable => $variableTitle) {
			$variables[$variable] = $this->$variable;
		}
		$this->variables = $variables;
	}

	/*
	* حذف منطقی
	*/
	public function softDelete()
	{
		if ($this->canDelete()) {
			$this->status = self::STATUS_DELETED;
			if ($this->save(false)) {
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

	public function afterFind()
	{
		foreach ((is_array($this->contract_clauses) ? $this->contract_clauses : []) as $index => $clause) {
			$this->clausesModels[] = new ContractClausesModel($clause);
		}
		parent::afterFind();
	}

	public static function itemAlias($type, $code = null)
	{
		$_items = [];
		switch ($type) {
			case 'Status';
				$_items = [
					self::STATUS_ACTIVE => Module::t('module', 'Status Active'),
					self::STATUS_CONFIRM => Module::t('module', 'Status Confirm'),
				];
				break;
			case 'ContractStaticVariables';
				$_items = [
					'start_date' => 'تاریخ شروع قرارداد',
					'end_date' => 'تاریخ پایان قرارداد',
					'month' => 'تعداد ماه',
					'daily_salary' => 'حقوق پایه روزانه',
					'right_to_housing' => 'حق مسکن',
					'right_to_food' => 'حق خواربار',
					'rightToChild' => 'حق اولاد',
				];
				break;
		}

		if (isset($code)) {
			return $_items[$code] ?? null;
		}
		return $_items ?: null;
	}

}
