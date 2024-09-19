<?php

namespace hesabro\hris\models;

use common\behaviors\ConvertDateToTimeBehavior;
use common\behaviors\JsonAdditional;
use common\behaviors\LogBehavior;
use common\behaviors\TraceBehavior;
use common\models\Tags;
use common\validators\DateValidator;
use Yii;
use backend\models\User;
use common\models\Faq;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
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
class Comfort extends ActiveRecord
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
                'targetClass' => User::class,
                'targetAttribute' => 'id',
                'allowArray' => true,
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            [
                ['jobs', 'excluded_jobs'],
                'exist',
                'targetClass' => Tags::class,
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
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Category'),
            'expire_time' => Yii::t('app', 'Expire Time'),
            'status' => Yii::t('app', 'Status'),
            'type_limit' => Yii::t('app', 'Type'),
            'count_limit' => 'تعداد مجاز',
            'amount_limit' => 'مبلغ مجاز',
            'description' => Yii::t('app', 'Description'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'changed' => Yii::t('app', 'Changed'),
            'users' => Yii::t('app', 'Users') . ' ' . Yii::t('app', 'Permitted'),
            'excluded_users' => Yii::t('app', 'Users') . ' ' . Yii::t('app', 'Not Permitted'),
            'jobs' => Yii::t('app', 'Jobs') . ' ' . Yii::t('app', 'Permitted'),
            'excluded_jobs' => Yii::t('app', 'Jobs') . ' ' . Yii::t('app', 'Not Permitted'),
            'married' => Yii::t('app', 'For Married'),
            'experience_limit' => Yii::t('app', 'Minimum Work Experience') . ' (' . Yii::t('app', 'Month') . ')',
            'request_again_limit' => Yii::t('app', 'Request Again') . ' (' . Yii::t('app', 'Day') . ')',
            'document_required' => Yii::t('app', 'Document Required'),
            'month_limit' => Yii::t('app', 'Month Limit'),
            'day_limit_start' => Yii::t('app', 'Day Limit Start'),
            'day_limit_end' => Yii::t('app', 'Day Limit End'),
            'related_faq' => Yii::t('app', 'Related Faq'),
            'related_faq_clause' => Yii::t('app', 'Related Faq Clause'),
            'salary_items_addition' => Yii::t('app', 'Salary Items Additions'),
            'showcase' => Yii::t('app', 'Just For Show')
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
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(User::className(), ['id' => 'update_id']);
    }

    /**
     * @return ComfortItemsQuery
     */
    public function getComfortItems(): ComfortItemsQuery
    {
        return $this->hasMany(ComfortItems::className(), ['comfort_id' => 'id']);
    }

    /**
     * Get comfort related faq
     * 
     * @return Faq|null
     */
    public function getRelatedFaq(): Faq|null
    {
        if ($this->related_faq)
            return Faq::find()->where(['id' => $this->related_faq])->one();

        return null;
    }

    /**
     * Get comfort related faq clause
     * 
     * @return FaqClause|null
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
            /** @var User $user */
            if (($user = User::findOne($userId)) !== null) {
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

        $this->related_faq = $this->related_faq;
        $this->related_faq_clause = $this->related_faq_clause;
        $this->jobs = $this->jobs ?: [];
        $this->excluded_jobs = $this->excluded_jobs ?: [];

        return parent::beforeSave($insert);
    }
}
