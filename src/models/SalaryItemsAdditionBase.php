<?php

namespace hesabro\hris\models;

use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\helpers\validators\DateValidator;
use hesabro\helpers\traits\CoreTrait;
use hesabro\hris\Module;
use Yii;

/**
 * This is the model class for table "mbt_employee_salary_items_addition".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $kind
 * @property int|null $type
 * @property int $second
 * @property int $from_date
 * @property int $to_date
 * @property string $description
 * @property int|null $status
 * @property int $creator_id
 * @property int|null $update_id
 * @property int|null $created
 * @property int|null $changed
 * @property int|null $is_auto
 * @property int|null $period_id
 *
 * @property object $user
 * @property SalaryPeriodItems $salaryItems
 * @property object $creator
 * @property object $update
 * @property string $title
 * @property int $convertValueToDay;
 * @property int $convertValueToHour;
 * @property int $convertValueToAmount;
 */
class SalaryItemsAdditionBase extends \yii\db\ActiveRecord
{
    use CoreTrait;

    const STATUS_DELETED = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_REJECT = 3;

    const KIND_LOW_TIME = 1; // کسر کار
    const KIND_OVER_TIME = 2; // اضافه کار
    const KIND_LEAVE_HOURLY = 3; // مرخصی ساعتی
    const KIND_LEAVE_DAILY = 4; // مرخصی روزانه
    const KIND_COMMISSION = 5; // پورسانت
    const KIND_COMMISSION_CONST = 6; // پورسانت ثابت
    const KIND_NON_CASH = 7; // مزایای غیر نقدی

    const SCENARIO_CREATE_LOW_TIME = 'create_low_time'; // کسر کار
    const SCENARIO_CREATE_OVER_TIME = 'create_over_time'; // اضافه کار
    const SCENARIO_CREATE_LEAVE_HOURLY = 'create_leave_hourly'; // مرخصی ساعتی
    const SCENARIO_CREATE_LEAVE_DAILY = 'create_leave_daily'; // مرخصی روزانه
    const SCENARIO_CREATE_COMMISSION = 'create_commission'; // پورسانت
    const SCENARIO_CREATE_COMMISSION_CONST = 'create_commission_const'; // پورسانت ثابت
    const SCENARIO_CREATE_NON_CASH = 'create_non_cash'; // مزایای غیر نقدی
    const SCENARIO_CREATE_AUTO = 'create_auto'; // خودکار

    const TYPE_LOW_DELAY = 1; // تاخیر
    const TYPE_LOW_RUSH = 2; // تعجیل
    const TYPE_LOW_ABSENCE = 3; // غیبت

    const TYPE_OVER_TIME_DAY = 4; // روز کاری
    const TYPE_OVER_TIME_HOLIDAY = 5; // تعطیل کاری
    const TYPE_OVER_TIME_NIGHT = 6;// شب کاری

    const TYPE_LEAVE_MERIT_HOURLY = 7; // استحقاقی ساعتی
    const TYPE_LEAVE_MERIT_DAILY = 8; // استحقاقی روزانه
    const TYPE_LEAVE_TREATMENT_DAILY = 9; // استعلاجی روزانه
    const TYPE_LEAVE_NO_SALARY_HOURLY = 10; // بدون حقوق ساعتی
    const TYPE_LEAVE_NO_SALARY_DAILY = 11; // بدون حقوق روزانه

    const TYPE_COMMISSION_REWARD = 12; // پاداش
    const TYPE_COMMISSION_BIRTHDAY = 13; // تولد

    const TYPE_COMMISSION_TWO_SHIFT = 14; // دو شیفت کاری
    const TYPE_COMMISSION_TWO_SHAREHOLDER = 15; // حق سهامداری
    const TYPE_COMMISSION_SPECIAL_DAY = 16; // روز خاص

    const TYPE_PAY_BUY = 17;

    const TYPE_NON_CASH_CREDIT_CARD = 18;

    const MERIT_HOURLY_REQUESTS_PER_DAY = 4;

    public $error_msg;

    public $date;

    public $range;

    public $total;

    public $year;

    public $month;
    public $total_merit_hourly;
    public $total_merit_daily;
    public $total_treatment_daily;
    public $total_no_salary_hourly;
    public $total_no_salary_daily;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_salary_items_addition}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'kind'], 'required'],
            [['type', 'second', 'date', 'description'], 'required', 'on' => [self::SCENARIO_CREATE_LOW_TIME, self::SCENARIO_CREATE_OVER_TIME]],
            [['type', 'second', 'range', 'description'], 'required', 'on' => [self::SCENARIO_CREATE_LEAVE_HOURLY, self::SCENARIO_CREATE_LEAVE_DAILY]],
            [['type', 'date', 'second', 'description'], 'required', 'on' => [self::SCENARIO_CREATE_COMMISSION, self::SCENARIO_CREATE_NON_CASH]],
            [['type', 'second', 'description'], 'required', 'on' => [self::SCENARIO_CREATE_COMMISSION_CONST]],
            [['date'], DateValidator::class, 'on' => [self::SCENARIO_CREATE_LOW_TIME, self::SCENARIO_CREATE_OVER_TIME]],
            [['date'], 'unique', 'targetAttribute' => ['user_id', 'kind', 'type', 'from_date'], 'on' => [self::SCENARIO_CREATE_LOW_TIME, self::SCENARIO_CREATE_OVER_TIME, self::SCENARIO_CREATE_COMMISSION, self::SCENARIO_CREATE_NON_CASH], 'message' => 'این عملیات برای این کارمند در این تاریخ قبلا ثبت شده است.لطفا از گزینه ویرایش استفاده نمایید.'],
            [['user_id', 'kind', 'type', 'second', 'from_date', 'to_date', 'status', 'creator_id', 'update_id', 'created', 'changed', 'period_id', 'is_auto'], 'integer'],
            [['second'], 'integer', 'min' => 1, 'on' => [self::SCENARIO_CREATE_COMMISSION, self::SCENARIO_CREATE_NON_CASH]],
            [['second'], 'integer', 'min' => 1, 'max' => (7.33 * 60), 'on' => [self::SCENARIO_CREATE_LOW_TIME]],
            [['second'], 'integer', 'min' => 1, 'max' => (24 * 60), 'on' => [self::SCENARIO_CREATE_OVER_TIME]],
            [['description'], 'string'],
            [['range'], 'validateMeritHourly', 'skipOnError' => true, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREATE_LEAVE_HOURLY, self::SCENARIO_CREATE_LEAVE_DAILY], 'when' => function ($model) {
                return $model->kind == self::KIND_LEAVE_HOURLY;
            }],

            [['range'], 'validateMeritDaily', 'skipOnError' => true, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREATE_LEAVE_HOURLY, self::SCENARIO_CREATE_LEAVE_DAILY], 'when' => function ($model) {
                return $model->kind == self::KIND_LEAVE_DAILY;
            }],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE_LOW_TIME] = ['user_id', '!kind', 'type', 'second', 'date', 'description'];
        $scenarios[self::SCENARIO_CREATE_OVER_TIME] = ['user_id', '!kind', 'type', 'second', 'date', 'description'];
        $scenarios[self::SCENARIO_CREATE_LEAVE_HOURLY] = ['user_id', '!kind', 'type', 'range', 'description'];
        $scenarios[self::SCENARIO_CREATE_LEAVE_DAILY] = ['user_id', '!kind', 'type', 'range', 'description'];
        $scenarios[self::SCENARIO_CREATE_COMMISSION] = ['user_id', '!kind', 'type', 'second', 'date', 'description'];
        $scenarios[self::SCENARIO_CREATE_NON_CASH] = ['user_id', '!kind', 'type', 'second', 'date', 'description'];
        $scenarios[self::SCENARIO_CREATE_AUTO] = ['user_id', 'kind', 'type', 'second', 'from_date', 'to_date', 'description'];

        return $scenarios;
    }

    public function beforeValidate()
    {
        if (in_array($this->getScenario(), [self::SCENARIO_CREATE_LOW_TIME, self::SCENARIO_CREATE_OVER_TIME, self::SCENARIO_CREATE_COMMISSION, self::SCENARIO_CREATE_NON_CASH])) {
            $this->from_date = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($this->date) . " 00:00:00");
        } elseif (in_array($this->getScenario(), [self::SCENARIO_CREATE_LEAVE_HOURLY])) {
            $date_range = $this->rangeToTimestampRange($this->range, "Y/m/d H:i:s", 1, " - ", 00, 00, 00);
            $this->from_date = $date_range['start'];
            $this->to_date = $date_range['end'];
            if (empty($this->from_date) || empty($this->to_date)) {
                $this->addError('range', Module::t('module', 'Invalid {value} .', ['value' => $this->getAttributeLabel('range')]));
            }
        } elseif (in_array($this->getScenario(), [self::SCENARIO_CREATE_LEAVE_DAILY])) {
            $date_range = $this->rangeToTimestampRange($this->range, "Y/m/d", 1, " - ", 00, 00, 00, true);
            $this->from_date = $date_range['start'];
            $this->to_date = $date_range['end'];
            if (empty($this->from_date) || empty($this->to_date)) {
                $this->addError('range', Module::t('module', 'Invalid {value} .', ['value' => $this->getAttributeLabel('range')]));
            }
        } elseif ($this->getScenario() == self::SCENARIO_CREATE_AUTO) {
            $this->from_date = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($this->from_date) . " 00:00:00");
            $this->to_date = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($this->to_date) . " 00:00:00");
        }
        return parent::beforeValidate();
    }


    /**
     * @param $attribute
     * بررسی مرخصی ساعتی
     */
    public function validateMeritHourly($attribute)
    {
        if (date('z', $this->from_date) !== date('z', $this->to_date)) {
            $this->addError($attribute, Module::t('module', 'Hourly leave can only be recorded in one day'));
        }

        $max_hours_leave = (int)self::MERIT_HOURLY_REQUESTS_PER_DAY;
        $sum = (($this->sumTodayLeaveHours() ?? 0)) - ($max_hours_leave * 60 * 60);
        $meritHourly_Requests = $sum <= 0 ? 1 : ceil(($sum / ($max_hours_leave * 60 * 60)) + 1);

        if (($this->to_date - $this->from_date) > ($max_hours_leave * 60 * 60)) {
            $this->addError($attribute, Module::t('module', "Maximum Hourly Leave per request is:{max_hours_leave}", ['max_hours_leave' => $max_hours_leave]));
        }
        if ($meritHourly_Requests <= self::MERIT_HOURLY_REQUESTS_PER_DAY) {

            $merit_hours_leftover = (($max_hours_leave * 60 * 60) * self::MERIT_HOURLY_REQUESTS_PER_DAY) - (($this->sumTodayLeaveHours() ?? 0));

            if (($this->to_date - $this->from_date) > $merit_hours_leftover) {
                $this->addError($attribute, Module::t('module', 'Your merit leave left for current request is  {merit_hour_leftover}', ['merit_hour_leftover' => Yii::$app->formatter->asDuration($merit_hours_leftover)]));
            }
        } else {
            $this->addError($attribute, Module::t('module', 'Sum of Your Hourly Merit Leaves Hours are Reached to This day Limit'));
        }
        $this->second = (int)($this->to_date - $this->from_date) / (60);

    }

    /**
     * @param $attribute
     * بررسی مرخصی ماهانه
     */
    public function validateMeritDaily($attribute)
    {
        if ($this->to_date - $this->from_date <= 0) {
            $this->addError($attribute, Module::t('module', "Minimum Range of Daily Leave is 1 Day and Days Start From daybreak"));
        } elseif ((date('z', $this->to_date) - date('z', $this->from_date)) > 1 && Yii::$app->jdf::jdate("Y/m", $this->from_date) != Yii::$app->jdf::jdate("Y/m", $this->to_date)) {
            $this->addError($attribute, 'مرخصی روزانه باید در یک ماه باشد.اگر در دوماه می باشد.لطفا ۲ مرخصی ثبت نمایید.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'user_id' => Module::t('module', 'User ID'),
            'kind' => 'عملیات',
            'type' => Module::t('module', 'Type'),
            'second' => $this->getScenario() == self::SCENARIO_CREATE_COMMISSION || $this->getScenario() == self::SCENARIO_CREATE_COMMISSION_CONST || $this->getScenario() == self::SCENARIO_CREATE_NON_CASH ? 'مبلغ (ریال)' : 'دقیقه',
            'date' => Module::t('module', 'Date'),
            'from_date' => Module::t('module', 'From Date'),
            'to_date' => Module::t('module', 'To Date'),
            'description' => Module::t('module', 'Description'),
            'status' => Module::t('module', 'State'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'created' => Module::t('module', 'Created'),
            'changed' => Module::t('module', 'Changed'),
            'range' => Module::t('module', 'Range'),
            'is_auto' => Module::t('module', 'Is Auto'),
        ];
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
    public function getSalaryItems()
    {
        return $this->hasMany(SalaryPeriodItems::class, ['user_id' => 'user_id']);
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
     * {@inheritdoc}
     * @return SalaryItemsAdditionQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new SalaryItemsAdditionQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return $this->status != self::STATUS_CONFIRM;
    }

    public function canDelete()
    {
        $confirmedSalaryPeriodExist = SalaryPeriod::find()
            ->byDate($this->from_date)
            ->byStatus(SalaryPeriod::STATUS_CONFIRM)
            ->exists();

        return !$confirmedSalaryPeriodExist && $this->status != self::STATUS_CONFIRM;
    }

    public function canConfirm()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM;
    }

    public function canReturnStatus()
    {
        return $this->status != self::STATUS_WAIT_CONFIRM;
    }

    public function canReject()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM;
    }

    public function sumMeritLeaves($user_id = null)
    {
        $user_id = $user_id ?? Yii::$app->user->id;
        $month_start = $this->getStartAndEndOfCurrentMonth()['start'];
        $month_end = $this->getStartAndEndOfCurrentMonth()['end'];
        $year_start = $this->getStartAndEndOfYear()['start'];
        $year_end = $this->getStartAndEndOfYear()['end'];
        $notInArr = [
            self::STATUS_REJECT,
            self::STATUS_WAIT_CONFIRM,
        ];

        $seconds['current_month'] = self::find()
            ->select(
                'SUM(CASE 
                    When to_date > ' . $month_end . ' AND from_date < ' . $month_end . ' AND from_date >=' . $month_start . ' THEN 1+' . $month_end . ' - from_date ' .
                '   When to_date > ' . $month_start . ' AND from_date < ' . $month_start . ' THEN to_date - ' . $month_start
                . ' When to_date <= 1+' . $month_end . ' AND from_date >=' . $month_start . ' THEN to_date - from_date
                 END)')
            ->andWhere(['in', 'type', [self::TYPE_LEAVE_MERIT_HOURLY, self::TYPE_LEAVE_MERIT_DAILY]])
            ->andWhere(['not in', 'status', $notInArr])
            ->andWhere(['user_id' => $user_id])
            ->scalar();

        $seconds['current_year'] = self::find()
            ->andWhere(['in', 'type', [self::TYPE_LEAVE_MERIT_HOURLY, self::TYPE_LEAVE_MERIT_DAILY]])
            ->andWhere(['not in', 'status', $notInArr])
            ->andWhere(['between', 'from_date', $year_start, $year_end])
            ->andWhere(['between', 'to_date', $year_start, $year_end])
            ->andWhere(['user_id' => $user_id])
            ->sum('to_date - from_date');

        return $seconds;
    }

    private function sumTodayLeaveHours()
    {
        $selected_day = Yii::$app->jdf::jdate("Y/m/d H:i:s", $this->from_date);

        $day_start_ts = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($selected_day) . " 00:00:00");
        $day_end_ts = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($selected_day) . " 23:59:59");

        $notInArr = [self::STATUS_REJECT];

        $query = self::find()
            ->andWhere(['in', 'type', [self::TYPE_LEAVE_MERIT_HOURLY]])
            ->andWhere(['not in', 'status', $notInArr])
            ->andWhere(['between', 'from_date', $day_start_ts, $day_end_ts])
            ->andWhere(['user_id' => Yii::$app->user->id])
            ->andWhere(['<>', 'id', $this->id]);

        return $query->sum('to_date - from_date');
    }


    /**
     * @return string
     */
    public function getValue()
    {
        switch ($this->kind) {
            case self::KIND_LOW_TIME:
            case self::KIND_OVER_TIME:
            case self::KIND_LEAVE_HOURLY:
                return $this->second . ' دقیقه';
            case self::KIND_COMMISSION:
            case self::KIND_COMMISSION_CONST:
            case self::KIND_NON_CASH:
                return number_format($this->second) . ' ریال';
            case self::KIND_LEAVE_DAILY:
                return Yii::$app->formatter->asDuration($this->to_date - $this->from_date, '  و ');
            default:
                return null;
        }
    }

    /**
     * @return string
     */
    public function getDate()
    {
        switch ($this->kind) {
            case self::KIND_LOW_TIME:
            case self::KIND_OVER_TIME:
            case self::KIND_COMMISSION:
            case self::KIND_NON_CASH:
                return Yii::$app->jdf::jdate("Y/m/d", $this->from_date);
            case self::KIND_LEAVE_HOURLY:
                return Yii::$app->jdf::jdate("Y/m/d H:i:s", $this->from_date);
            case self::KIND_LEAVE_DAILY:
                return Yii::$app->jdf::jdate("Y/m/d", $this->from_date) . ' - ' . Yii::$app->jdf::jdate("Y/m/d", $this->to_date);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = self::itemAlias('Kind', $this->kind) . ' - ' . self::itemAlias('Type', $this->type) . ' - ' . $this->getValue() . ' - ';
        if ($this->kind == self::KIND_COMMISSION || self::KIND_NON_CASH || $this->kind == self::KIND_COMMISSION_CONST) {
            $title .= $this->description;
        } else {
            $title .= $this->getDate();
        }
        return $title;
    }

    /**
     * @return int
     */
    public function getConvertValueToDay()
    {
        return (int)((int)($this->to_date - $this->from_date) / (60 * 60 * 24));
    }

    /**
     * @return float
     */
    public function getConvertValueToHour()
    {
        return (float)(round((int)($this->second) / (60), 2));
    }

    /**
     * @return float
     */
    public function getConvertValueToAmount()
    {
        return (int)$this->second;
    }

    /**
     * @return bool
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

    /**
     * @return bool
     */
    public function confirm()
    {
        $this->status = self::STATUS_CONFIRM;
        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function returnStatus()
    {
        $this->status = self::STATUS_WAIT_CONFIRM;
        return $this->save(false);
    }

    /**
     * @param $description
     * @return bool
     */
    public function reject($description)
    {
        $this->status = self::STATUS_REJECT;
        $this->description .= ' <br /> رد شده به علت <br />' . $description;
        return $this->save(false);
    }

    public function setDefaultValueBeforeCreate()
    {
        switch ($this->kind) {
            case self::KIND_LOW_TIME:
                $this->setScenario(self::SCENARIO_CREATE_LOW_TIME);
                $this->date = Yii::$app->jdf::jdate("Y/m/d");
                break;
            case self::KIND_OVER_TIME:
                $this->setScenario(self::SCENARIO_CREATE_OVER_TIME);
                $this->date = Yii::$app->jdf::jdate("Y/m/d");
                break;
            case self::KIND_LEAVE_HOURLY:
                $this->setScenario(self::SCENARIO_CREATE_LEAVE_HOURLY);
                break;
            case self::KIND_LEAVE_DAILY:
                $this->setScenario(self::SCENARIO_CREATE_LEAVE_DAILY);
                break;
            case self::KIND_COMMISSION:
                $this->setScenario(self::SCENARIO_CREATE_COMMISSION);
                break;
            case self::KIND_COMMISSION_CONST:
                $this->setScenario(self::SCENARIO_CREATE_COMMISSION_CONST);
                break;
            case self::KIND_NON_CASH:
                $this->setScenario(self::SCENARIO_CREATE_NON_CASH);
                break;
        }
    }

    public function setDefaultValueBeforeUpdate()
    {
        switch ($this->kind) {
            case self::KIND_LOW_TIME:
                $this->setScenario(self::SCENARIO_CREATE_LOW_TIME);
                $this->date = Yii::$app->jdf::jdate("Y/m/d", $this->from_date);
                break;
            case self::KIND_OVER_TIME:
                $this->setScenario(self::SCENARIO_CREATE_OVER_TIME);
                $this->date = Yii::$app->jdf::jdate("Y/m/d", $this->from_date);
                break;
            case self::KIND_LEAVE_HOURLY:
                $this->setScenario(self::SCENARIO_CREATE_LEAVE_HOURLY);
                $this->range = Yii::$app->jdf::jdate("Y/m/d H:i:s", $this->from_date) . ' - ' . Yii::$app->jdf::jdate("Y/m/d H:i:s", $this->to_date);
                break;
            case self::KIND_LEAVE_DAILY:
                $this->setScenario(self::SCENARIO_CREATE_LEAVE_DAILY);
                $this->range = Yii::$app->jdf::jdate("Y/m/d", $this->from_date) . ' - ' . Yii::$app->jdf::jdate("Y/m/d", $this->to_date);
                break;
            case self::KIND_COMMISSION:
                $this->setScenario(self::SCENARIO_CREATE_COMMISSION);
                $this->date = Yii::$app->jdf::jdate("Y/m/d", $this->from_date);
                break;
            case self::KIND_COMMISSION_CONST:
                $this->setScenario(self::SCENARIO_CREATE_COMMISSION_CONST);
                break;
            case self::KIND_NON_CASH:
                $this->setScenario(self::SCENARIO_CREATE_NON_CASH);
                $this->date = Yii::$app->jdf::jdate("Y/m/d", $this->from_date);
                break;
        }
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
            'Status' => [
                self::STATUS_WAIT_CONFIRM => Module::t('module', 'Wait Confirm'),
                self::STATUS_CONFIRM => Module::t('module', 'Confirm'),
                self::STATUS_REJECT => Module::t('module', 'Reject'),
            ],
            'StatusClass' => [
                self::STATUS_WAIT_CONFIRM => 'warning',
                self::STATUS_CONFIRM => 'success',
                self::STATUS_REJECT => 'danger',
            ],
            'Kind' => [
                self::KIND_LOW_TIME => 'کسر کار',
                self::KIND_OVER_TIME => 'اضافه کار',
                self::KIND_LEAVE_HOURLY => 'مرخصی ساعتی',
                self::KIND_LEAVE_DAILY => 'مرخصی روزانه',
                self::KIND_COMMISSION => 'پورسانت',
                self::KIND_COMMISSION_CONST => 'پورسانت ثابت',
                self::KIND_NON_CASH => 'مزایای غیر نقدی',
            ],
            'Type' => [
                self::TYPE_LOW_DELAY => "تاخیر",
                self::TYPE_LOW_RUSH => "تعجیل",
                self::TYPE_LOW_ABSENCE => "غیبت",
                self::TYPE_OVER_TIME_DAY => "روز کاری",
                self::TYPE_OVER_TIME_HOLIDAY => "تعطیل کاری",
                self::TYPE_OVER_TIME_NIGHT => "شب کار",
                self::TYPE_LEAVE_MERIT_HOURLY => "استحقاقی ساعتی",
                self::TYPE_LEAVE_MERIT_DAILY => "استحقاقی روزانه",
                self::TYPE_LEAVE_TREATMENT_DAILY => "استعلاجی روزانه",
                //self::TYPE_LEAVE_NO_SALARY_HOURLY => "بدون حقوق ساعتی",
                self::TYPE_LEAVE_NO_SALARY_DAILY => "بدون حقوق روزانه",
                self::TYPE_COMMISSION_REWARD => "پاداش",
                self::TYPE_COMMISSION_BIRTHDAY => "هدیه تولد",
                self::TYPE_COMMISSION_SPECIAL_DAY => "هدیه خاص",
                self::TYPE_COMMISSION_TWO_SHIFT => "دو شیفت کاری",
                self::TYPE_COMMISSION_TWO_SHAREHOLDER => "حق سهامداری",
                self::TYPE_PAY_BUY => "پی بای",
                self::TYPE_NON_CASH_CREDIT_CARD => "کارت کارانه",
            ],
            'TypeLow' => [
                self::TYPE_LOW_DELAY => "تاخیر",
                self::TYPE_LOW_RUSH => "تعجیل",
                self::TYPE_LOW_ABSENCE => "غیبت",
            ],
            'TypeOver' => [
                self::TYPE_OVER_TIME_DAY => "روز کاری",
                self::TYPE_OVER_TIME_HOLIDAY => "تعطیل کاری",
                self::TYPE_OVER_TIME_NIGHT => "شب کار",
            ],
            'TypeLeaveHourly' => [
                self::TYPE_LEAVE_MERIT_HOURLY => "استحقاقی",
                //self::TYPE_LEAVE_NO_SALARY_HOURLY => "بدون حقوق",
            ],
            'TypeLeaveDaily' => [
                self::TYPE_LEAVE_MERIT_DAILY => "استحقاقی",
                self::TYPE_LEAVE_TREATMENT_DAILY => "استعلاجی",
                self::TYPE_LEAVE_NO_SALARY_DAILY => "بدون حقوق",
            ],
            'TypeCommission' => [
                self::TYPE_COMMISSION_REWARD => "پاداش",
                self::TYPE_COMMISSION_BIRTHDAY => "هدیه تولد",
                self::TYPE_COMMISSION_SPECIAL_DAY => "هدیه خاص",
            ],
            'TypeCommissionConst' => [
                self::TYPE_COMMISSION_TWO_SHIFT => "دو شیفت کاری",
                self::TYPE_COMMISSION_TWO_SHAREHOLDER => "حق سهامداری",
            ],
            'TypeNonCash' => [
                self::TYPE_PAY_BUY => "پی بای",
                self::TYPE_NON_CASH_CREDIT_CARD => 'کارت کارانه'
            ],
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
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => 'backend\modules\employee\models\SalaryItemsAddition',
                'saveAfterInsert' => true
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
            if ($this->getScenario() == self::SCENARIO_CREATE_AUTO) {
                $this->is_auto = Yii::$app->helper::CHECKED;
            } else {
                $this->is_auto = Yii::$app->helper::UN_CHECKED;
                $this->status = self::STATUS_WAIT_CONFIRM;
            }
        }
        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
    }
}
