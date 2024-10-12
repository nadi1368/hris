<?php

namespace hesabro\hris\models;

use hesabro\helpers\behaviors\ConvertDateToTimeBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\helpers\validators\DateValidator;
use hesabro\hris\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%employee_comfort}}".
 *
 * @property int $id
 * @property string $title
 * @property int $type
 * @property int $expire_time
 * @property int $status
 * @property int $type_limit
 * @property int $count_limit
 * @property float $amount_limit
 * @property string|null $description
 * @property string|null $additional_data
 * @property int $created
 * @property int $creator_id
 * @property int $update_id
 * @property int $changed
 *
 * @property string $usersList
 * @property ComfortItems[] $comfortItems
 */
class ComfortBase extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    const TYPE_LIMIT_MONTHLY = 1;
    const TYPE_LIMIT_YEARLY = 2;

    const CAT_TYPE_COMFORT = 1;
    const CAT_TYPE_MEDICAL = 2;

    const CAT_TYPE_LOAN = 3;

    const CAT_TYPE_FINANCIAL = 4;
    const CAT_TYPE_OTHER = 5;

    const SALARY_ITEM_IGNORE = 0;

    const SCENARIO_CREATE = 'create';

    const SCENARIO_UPDATE = 'update';

    public $users;

    public mixed $excluded_users = [];

    public mixed $excluded_jobs = [];
    public mixed $jobs = [];

    public bool $married = false;

    public mixed $experience_limit = null;

    public mixed $request_again_limit = null;

    public mixed $document_required = false;

    public mixed $month_limit = null;

    public mixed $day_limit_start = null;

    public mixed $day_limit_end = null;

    public mixed $related_faq = null;

    public mixed $related_faq_clause = null;

    public mixed $salary_items_addition = null;

    public mixed $showcase = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_comfort}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type', 'type_limit', 'salary_items_addition'], 'required'],
            [
                [
                    'type', 'status', 'created', 'creator_id', 'update_id', 'changed', 'experience_limit',
                    'request_again_limit', 'day_limit_start', 'day_limit_end', 'related_faq'
                ],
                'integer'
            ],
            [['count_limit', 'type_limit'], 'integer', 'min' => 0],
            [['count_limit', 'amount_limit', 'expire_time'], 'default', 'value' => 0],
            [['amount_limit'], 'number'],
            [['married', 'showcase'], 'boolean'],
            [['users'], 'filter', 'filter' => function ($value) {
                return (is_array($value) ? $value : []);
            }, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['expire_time'], DateValidator::class, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['related_faq_clause', 'description'], 'string'],
            [['additional_data'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['type'], 'validateTypeRequirements', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['document_required'], 'boolean'],
            [
                ['users', 'excluded_users'],
                'exist',
                'targetClass' => Module::getInstance()->user,
                'targetAttribute' => 'id',
                'allowArray' => true,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['jobs', 'excluded_jobs'],
                'exist',
                'targetClass' => SalaryInsurance::class,
                'targetAttribute' => 'id',
                'allowArray' => true,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                'month_limit',
                'each',
                'rule' => [
                    'number',
                    'min' => 1,
                    'max' => 12
                ]
            ],
            [
                ['day_limit_start', 'day_limit_end'],
                'number',
                'min' => 0,
                'max' => 31
            ],
            [
                'day_limit_end',
                'compare',
                'compareAttribute' => 'day_limit_start',
                'operator' => '>',
                'type' => 'number',
                'when' => fn (self $model) => $model->day_limit_start && $this->day_limit_end
            ],
            [
                'salary_items_addition',
                'in',
                'range' => array_keys(self::itemAlias('SalaryItemsAddition')),
            ]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = [
            'title', 'type', 'type_limit', 'count_limit', 'expire_time', 'amount_limit', 'description',
            'users', 'excluded_users', 'jobs', 'excluded_jobs', 'married', 'experience_limit', 'request_again_limit',
            'document_required', 'month_limit', 'day_limit_start', 'day_limit_end', 'related_faq', 'related_faq_clause',
            'salary_items_addition', 'showcase'
        ];
        $scenarios[self::SCENARIO_UPDATE] = [
            'title', 'type', 'type_limit', 'count_limit', 'expire_time', 'amount_limit', 'description',
            'users', 'excluded_users', 'jobs', 'excluded_jobs', 'married', 'experience_limit', 'request_again_limit',
            'document_required', 'month_limit', 'day_limit_start', 'day_limit_end', 'related_faq', 'related_faq_clause',
            'salary_items_addition', 'showcase'
        ];

        return $scenarios;
    }

    public function validateTypeRequirements()
    {
        if (((int) $this->type) === self::CAT_TYPE_LOAN && !$this->amount_limit) {
            $this->addError('amount_limit', 'برای ایجاد این نوع امکان رفاهی مبلغ مجاز الزامی است.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'type' => Module::t('module', 'Category'),
            'expire_time' => Module::t('module', 'Expire Time'),
            'status' => Module::t('module', 'Status'),
            'type_limit' => Module::t('module', 'Type'),
            'count_limit' => 'تعداد مجاز',
            'amount_limit' => 'مبلغ مجاز',
            'description' => Module::t('module', 'Description'),
            'additional_data' => Module::t('module', 'Additional Data'),
            'created' => Module::t('module', 'Created'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'changed' => Module::t('module', 'Changed'),
            'users' => Module::t('module', 'Users') . ' ' . Module::t('module', 'Permitted'),
            'excluded_users' => Module::t('module', 'Users') . ' ' . Module::t('module', 'Not Permitted'),
            'jobs' => Module::t('module', 'Jobs') . ' ' . Module::t('module', 'Permitted'),
            'excluded_jobs' => Module::t('module', 'Jobs') . ' ' . Module::t('module', 'Not Permitted'),
            'married' => Module::t('module', 'For Married'),
            'experience_limit' => Module::t('module', 'Minimum Work Experience') . ' (' . Module::t('module', 'Month') . ')',
            'request_again_limit' => Module::t('module', 'Request Again') . ' (' . Module::t('module', 'Day') . ')',
            'document_required' => Module::t('module', 'Document Required'),
            'month_limit' => Module::t('module', 'Month Limit'),
            'day_limit_start' => Module::t('module', 'Day Limit Start'),
            'day_limit_end' => Module::t('module', 'Day Limit End'),
            'related_faq' => Module::t('module', 'Related Faq'),
            'related_faq_clause' => Module::t('module', 'Related Faq Clause'),
            'salary_items_addition' => Module::t('module', 'Salary Items Additions'),
            'showcase' => Module::t('module', 'Just For Show')
        ];
    }

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'request_again_limit' => 'از زمان ایجاد درخواست تایید شده محاسبه می‌شود.'
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'update_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComfortItems(): ActiveQuery
    {
        return $this->hasMany(Module::getInstance()->user, ['comfort_id' => 'id']);
    }

    /**
     * Get comfort related faq
     *
     * @return Content|null
     */
    public function getRelatedFaq(): Content|null
    {
        if ($this->related_faq) {
            return Content::find()->where(['id' => $this->related_faq])->one();
        }

        return null;
    }

    /**
     * Get comfort related faq clause
     *
     * @return ContentClause|null
     */
    public function getRelatedFaqClause()
    {
        $faq = $this->getRelatedFaq();
        if ($faq && $this->related_faq_clause && $faq->clauses && count($faq->clauses)) {
            return array_values(array_filter($faq->clauses, fn ($clause) => $clause['id'] == $this->related_faq_clause))[0] ?? null;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getUsersList(): string
    {
        $list = '';
        foreach ($this->users as $userId) {
            if (($user = Module::getInstance()->user::findOne($userId)) !== null) {
                $list .= '<label class="badge badge-info mr-2 mb-2">' . $user->fullName . ' </label> ';
            }
        }
        return $list;
    }

    /**
     * {@inheritdoc}
     * @return ComfortQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ComfortQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        if ($this->getComfortItems()->exists()) {
            return false;
        }
        return true;
    }

    public function softDelete(): bool
    {
        $this->status = self::STATUS_DELETED;
        if ($this->save()) {
            return true;
        } else {
            return false;
        }
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'TypeCat' => [
                self::CAT_TYPE_COMFORT => 'رفاهی',
                self::CAT_TYPE_MEDICAL => 'پزشکی',
                self::CAT_TYPE_LOAN => 'وام',
                self::CAT_TYPE_FINANCIAL => 'هزینه',
                self::CAT_TYPE_OTHER => 'سایر',
            ],
            'TypeLimit' => [
                self::TYPE_LIMIT_MONTHLY => 'ماهیانه',
                self::TYPE_LIMIT_YEARLY => 'سالیانه',
            ],
            'CatBg' => [
                self::CAT_TYPE_COMFORT => ['#10b981', '#059669'],
                self::CAT_TYPE_MEDICAL => ['#06b6d4', '#0891b2'],
                self::CAT_TYPE_LOAN => ['#f43f5e', '#e11d48'],
                self::CAT_TYPE_FINANCIAL => ['#0ea5e9', '#0284c7'],
                self::CAT_TYPE_OTHER => ['#f59e0b', '#d97706']
            ],
            'CatColor' => [
                self::CAT_TYPE_COMFORT => '#ffffff',
                self::CAT_TYPE_MEDICAL => '#ffffff',
                self::CAT_TYPE_LOAN => '#ffffff',
                self::CAT_TYPE_FINANCIAL => '#ffffff',
                self::CAT_TYPE_OTHER => '#ffffff'
            ],
            'SalaryItemsAddition' => [
                self::SALARY_ITEM_IGNORE => 'بی‌تاثیر',
                SalaryItemsAddition::TYPE_COMMISSION_REWARD => "پاداش",
                SalaryItemsAddition::TYPE_COMMISSION_BIRTHDAY => "هدیه تولد",
                SalaryItemsAddition::TYPE_COMMISSION_SPECIAL_DAY => "هدیه خاص",
                SalaryItemsAddition::TYPE_PAY_BUY => "پی بای",
                SalaryItemsAddition::TYPE_NON_CASH_CREDIT_CARD => 'کارت کارانه'
            ]
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'changed'
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'update_id'
            ],
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => ConvertDateToTimeBehavior::class,
                'attributes' => ['expire_time'],
                'scenarios' => [
                    self::SCENARIO_CREATE, self::SCENARIO_UPDATE
                ],
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'users' => 'Array',
                    'excluded_users' => 'Array',
                    'excluded_jobs' => 'StringArray',
                    'jobs' => 'StringArray',
                    'married' => 'Boolean',
                    'document_required' => 'Boolean',
                    'experience_limit' => 'NullInteger',
                    'request_again_limit' => 'NullInteger',
                    'month_limit' => 'Array',
                    'day_limit_start' => 'NullInteger',
                    'day_limit_end' => 'NullInteger',
                    'related_faq' => 'NullInteger',
                    'related_faq_clause' => 'String',
                    'salary_items_addition' => 'String',
                    'showcase' => 'Boolean'
                ],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        $this->month_limit = array_filter($this->month_limit ?: []);
        $this->jobs = $this->jobs ?: [];
        $this->excluded_jobs = $this->excluded_jobs ?: [];

        return parent::beforeSave($insert);
    }
}
