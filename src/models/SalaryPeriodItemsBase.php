<?php

namespace hesabro\hris\models;

use backend\models\BalanceDaily;
use backend\models\User;
use common\behaviors\JsonAdditional;
use common\behaviors\LogBehavior;
use common\behaviors\TraceBehavior;
use common\components\jdf\Jdf;
use common\models\BalanceDetailed;
use common\models\Settings;
use common\models\UserPoints;
use common\models\Year;
use console\job\SmsArrayJob;
use Yii;

/**
 * This is the model class for table "{{%employee_salary_period_items}}".
 *
 * @property int $id
 * @property int $period_id
 * @property int $user_id
 * @property int $hours_of_work
 * @property int $basic_salary
 * @property int $cost_of_house
 * @property int $cost_of_food
 * @property int $cost_of_spouse
 * @property int $cost_of_children
 * @property int $count_of_children
 * @property int $cost_of_year حق سنوات
 * @property int $rate_of_year نرخ سنوات سلالانه
 * @property int $hours_of_overtime
 * @property int $holiday_of_overtime
 * @property int $night_of_overtime
 * @property int $commission پورسانت
 * @property int $non_cash_commission  مزایای غیر نقدی
 * @property int $insurance بیمه
 * @property int $insurance_owner بیمه کارفرما
 * @property int $insurance_addition بیمه تکمیلی
 * @property int $tax مالیات
 * @property int $total_salary
 * @property int $advance_money مبلغ مساعده این ماه
 * @property int $cost_of_trust حق مسئولیت
 * @property int $payment_salary
 * @property int $can_payment
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 *
 * @property SalaryPeriod $period
 * @property User $user
 * @property Year $year
 * @property EmployeeBranchUser $employee
 * @property int $historyOfWork
 * @property int $historyOfWorkConvertToYear
 * @property int $advanceMoneyUntilThisMonth
 * @property int $yearPaymentBeforeThisPeriod
 * @property int $lastSalaryThisYear // آخرین حقوق دریافتنی
 * @property int $contWorkThisYear // کارکرد امسال
 * @property int $finalPayment // پرداختی حقوق
 */
class SalaryPeriodItemsBase extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_AFTER_CONFIRM = 'update_after_confirm';
    const SCENARIO_CREATE_REWARD = 'create_reward';
    const SCENARIO_UPDATE_REWARD = 'update_reward';
    const SCENARIO_CREATE_YEAR = 'create_year';
    const SCENARIO_UPDATE_YEAR = 'update_year';


    public $hours_of_overtime_cost, $holiday_of_overtime_cost, $night_of_overtime_cost, $final_payment;

    /** @var Year */
    public $yearModel = null;

    private $_historyOfWork = null;

    /** additional data */
    public $count_point = 0; //تعداد امتیاز
    public $cost_point = 0; // میزان امتیاز
    public $treatment_day = 0; // روز استعلاج
    public $description; // توضیحات
    public $salary_decrease = 0; // کاهش حقوق
    public $yearPaymentBeforeThisPeriod;
    public $yearCountThisPeriod; // کارکرد امسال در سنوات
    public $yearAmountThisPeriod; // سنوات امسال
    public $countOfDayLeaveNoSalary; // مرخصی بدون حقوق
    public $hoursOfLowTime; //;کسر کار ساعت
    public $hoursOfLowTimeCost; //;کسر کار مبلغ
    public $detailAddition; // جزییات اضافات کسورات حقوق
    public $descriptionShowEmployee;  // توضیحات قابل نمایش به کارمند

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_salary_period_items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hours_of_work', 'basic_salary', 'cost_of_house', 'cost_of_food', 'cost_of_spouse', 'cost_of_children', 'count_of_children'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['hours_of_work', 'basic_salary', 'total_salary', 'payment_salary'], 'required', 'on' => [self::SCENARIO_CREATE_REWARD, self::SCENARIO_UPDATE_REWARD, self::SCENARIO_CREATE_YEAR, self::SCENARIO_UPDATE_YEAR]],
            [['hours_of_overtime'], 'number', 'max' => 120, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['period_id', 'user_id', 'count_of_children', 'can_payment', 'creator_id', 'update_id', 'created', 'changed', 'salary_decrease', 'non_cash_commission'], 'integer'],
            [['basic_salary', 'cost_of_house', 'cost_of_food', 'cost_of_spouse', 'cost_of_children', 'cost_of_year', 'rate_of_year', 'hours_of_overtime', 'holiday_of_overtime', 'night_of_overtime', 'hoursOfLowTime', 'commission', 'insurance_owner', 'insurance_addition', 'insurance', 'tax', 'total_salary', 'advance_money', 'cost_of_trust', 'payment_salary'], 'number'],
            [['basic_salary', 'cost_of_house', 'cost_of_food', 'cost_of_spouse', 'cost_of_children', 'cost_of_year', 'rate_of_year', 'hours_of_overtime', 'holiday_of_overtime', 'night_of_overtime', 'hoursOfLowTime', 'commission', 'insurance_owner', 'insurance_addition', 'insurance', 'tax', 'total_salary', 'advance_money', 'cost_of_trust', 'payment_salary'], 'default', 'value' => 0, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['period_id', 'user_id'], 'unique', 'targetAttribute' => ['period_id', 'user_id']],
            [['description', 'descriptionShowEmployee'], 'string'],
            [['hours_of_work'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['hours_of_work'], 'integer', 'min' => 1, 'max' => 366, 'on' => [self::SCENARIO_CREATE_REWARD, self::SCENARIO_UPDATE_REWARD]],
            [['advance_money'], 'integer', 'min' => 0, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['treatment_day'], 'integer', 'min' => 0, 'max' => 31, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_UPDATE_AFTER_CONFIRM]],
            [['hours_of_work'], 'validateHoursOfWork', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['treatment_day'], 'validateHoursOfWork', 'on' => [self::SCENARIO_UPDATE_AFTER_CONFIRM]],
            [['salary_decrease'], 'integer', 'min' => 0, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['count_point'], 'integer', 'min' => 0, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['basic_salary'], 'validateBasicSalary', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['count_point'], 'validateUserPoint', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['non_cash_commission'], 'validateNonCashCommission', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['advance_money'], 'compare', 'compareAttribute' => 'payment_salary', 'operator' => '<=', 'type' => 'number', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalaryPeriod::class, 'targetAttribute' => ['period_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['hours_of_work', 'basic_salary', 'cost_of_house', 'cost_of_food', 'cost_of_spouse', 'cost_of_children', 'count_of_children', 'cost_of_year', 'rate_of_year', 'total_salary', 'advance_money', 'cost_of_trust', 'payment_salary', 'hours_of_overtime', 'holiday_of_overtime', 'night_of_overtime', 'hoursOfLowTime', 'count_point', 'salary_decrease', 'treatment_day', 'description', 'descriptionShowEmployee', 'non_cash_commission'];
        $scenarios[self::SCENARIO_UPDATE] = ['hours_of_work', 'basic_salary', 'cost_of_house', 'cost_of_food', 'cost_of_spouse', 'cost_of_children', 'count_of_children', 'cost_of_year', 'rate_of_year', 'total_salary', 'advance_money', 'cost_of_trust', 'payment_salary', 'hours_of_overtime', 'holiday_of_overtime', 'night_of_overtime', 'hoursOfLowTime', 'count_point', 'salary_decrease', 'treatment_day', 'description', 'descriptionShowEmployee', 'non_cash_commission'];
        $scenarios[self::SCENARIO_UPDATE_AFTER_CONFIRM] = ['treatment_day', 'description', 'descriptionShowEmployee'];
        $scenarios[self::SCENARIO_CREATE_REWARD] = ['hours_of_work', 'basic_salary', 'total_salary', 'advance_money', 'payment_salary'];
        $scenarios[self::SCENARIO_UPDATE_REWARD] = ['hours_of_work', 'basic_salary', 'total_salary', 'advance_money', 'payment_salary'];
        $scenarios[self::SCENARIO_CREATE_YEAR] = ['hours_of_work', 'basic_salary', 'total_salary', 'advance_money', 'payment_salary'];
        $scenarios[self::SCENARIO_UPDATE_YEAR] = ['hours_of_work', 'basic_salary', 'total_salary', 'advance_money', 'payment_salary'];

        return $scenarios;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateHoursOfWork($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (($this->hours_of_work + $this->treatment_day) > $this->period->countDay) {
                $this->addError($attribute, 'تعداد روز کارکرد و استعلاجی بیشتر از تعداد روز ماه می باشد.');
            }
            if (($this->hours_of_work + $this->treatment_day) < 1) {
                $this->addError($attribute, 'تعداد روز کارکرد و استعلاجی نمی تواند کمتر از یک باشد.');
            }

        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateBasicSalary($attribute, $params)
    {
        if (!$this->hasErrors() && !($this->employee->end_work > 0) && $this->basic_salary < $this->year->MIN_BASIC_SALARY && $this->hours_of_work > 0) {
            $this->addError($attribute, 'نمی تواند کمتر از حداقل حقوق باشد.');
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateUserPoint($attribute, $params)
    {
        if (!$this->hasErrors() && $this->count_point > 0) {
            $countAllow = UserPoints::countRequestPayment($this->user_id);
            if ($this->count_point > $countAllow) {
                $this->addError($attribute, 'تعداد امتیاز مجاز برای برگشت وجه ' . $countAllow . ' می باشد');
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateNonCashCommission($attribute, $params)
    {
        if (!$this->hasErrors() && $this->non_cash_commission > 0) {
            $year = $this->period->year;
            if ($this->employee->start_work > $year->start) {
                $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->employee->start_work) . ' 00:00:00');
            } else {
                $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($year->start) . ' 00:00:00');
            }

            $totalNonCashCommission = SalaryPeriodItems::find()
                    ->andWhere(['user_id' => $this->user_id])
                    ->andWhere(['<>', SalaryPeriodItems::tableName() . '.id', (int)$this->id])
                    ->andWhere(['>=', 'start_date', $startTime])
                    ->bySalary()
                    ->sum('non_cash_commission') + (int)$this->non_cash_commission;

            if ($totalNonCashCommission > ($year->COST_TAX_STEP_1_MIN * 2)) {
                $this->addError($attribute, 'مبلغ مورد نظر از سقف مجاز معافیت مالیاتی بیشتر می باشد');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'period_id' => Yii::t('app', 'Period ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'hours_of_work' => 'کارکرد',
            'basic_salary' => 'دستمزد',
            'cost_of_house' => 'حق مسکن',
            'cost_of_food' => 'حق بن',
            'cost_of_spouse' => 'حق عائله مندی',
            'cost_of_children' => 'حق اولاد',
            'count_of_children' => 'تعداد اولاد',
            'cost_of_year' => '',
            'rate_of_year' => 'سنوات',
            'hours_of_overtime' => 'اضافه کاری',
            'holiday_of_overtime' => 'تعطیل کاری',
            'night_of_overtime' => 'شب کاری',
            'commission' => 'پاداش',
            'insurance' => 'بیمه',
            'insurance_owner' => 'بیمه کارفرا',
            'insurance_addition' => 'بیمه تکمیلی',
            'tax' => 'مالیات',
            'total_salary' => 'ناخالص',
            'advance_money' => 'مساعده',
            'payment_salary' => 'خالص',
            'cost_of_trust' => 'حق مسئولیت',
            'can_payment' => 'پرداخت شود',
            'count_point' => 'تعداد امتیاز',
            'cost_point' => 'مبلغ امتیاز',
            'final_payment' => 'پرداختی',
            'treatment_day' => 'استعلاجی',
            'yearPaymentBeforeThisPeriod' => 'سنوات سال های قبل',
            'yearCountThisPeriod' => 'کارکرد امسال',
            'yearAmountThisPeriod' => 'سنوات امسال',
            'description' => Yii::t('app', 'Description'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'created' => Yii::t('app', 'Created'),
            'changed' => Yii::t('app', 'Changed'),
            'salary_decrease' => 'کسر حقوق',
            'countOfDayLeaveNoSalary' => 'مرخصی بدون حقوق',
            'hoursOfLowTime' => 'کسر کار',
            'hoursOfLowTimeCost' => 'مبلغ کسر کار',
            'non_cash_commission' => 'مزایای غیر نقدی',
            'descriptionShowEmployee' => 'توضیحات قابل نمایش به کارمند',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriod()
    {
        return $this->hasOne(SalaryPeriod::class, ['id' => 'period_id']);
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
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'update_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(EmployeeBranchUser::class, ['user_id' => 'user_id']);
    }

    /**
     * @return array|Year|null
     */
    public function getYear()
    {
        if ($this->yearModel === null) {
            return Year::find()->byDate(Yii::$app->jdate->date("Y/m/d", $this->period->start_date))->one();
        }
        return $this->yearModel;
    }

    public function setHistoryOfWork()
    {
        $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->employee->start_work) . ' 00:00:00');
        $this->_historyOfWork =
            (int)self::find()
                ->andWhere(['user_id' => $this->user_id])
                ->andWhere(['<>', SalaryPeriodItems::tableName() . '.id', (int)$this->id])
                ->andWhere(['>=', 'start_date', $startTime])
                ->bySalary()
                ->sum('hours_of_work') +
            (int)SalaryPeriodItems::find()
                ->andWhere(['user_id' => $this->user_id])
                ->andWhere(['<>', SalaryPeriodItems::tableName() . '.id', (int)$this->id])
                ->andWhere(['>=', 'start_date', $startTime])
                ->bySalary()
                ->sum('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.treatment_day")');
    }

    public function getHistoryOfWork()
    {
        return $this->_historyOfWork;
    }

    /**
     * @return int
     */
    public function getHistoryOfWorkConvertToYear(): int
    {
        if ($this->_historyOfWork === null) {
            $this->setHistoryOfWork();
        }
        $hours_of_work = $this->_historyOfWork;
        if ($this->employee !== null && $this->employee->work_history_day_count > 0) {
            $hours_of_work += (int)$this->employee->work_history_day_count;
        }
        return ((int)($hours_of_work / 365));
    }

    /**
     * @return float|int|mixed
     */
    public function getRateOfYearFromHistory()
    {
        $countYearOfWork = $this->getHistoryOfWorkConvertToYear();
        if (($rateOfYear = RateOfYearSalary::find()->byYear($countYearOfWork)->one()) !== null) {
            return $rateOfYear->rate_of_day * $this->hours_of_work;
        } else {
            return $this->year->COST_OF_YEAR;
        }
    }

    public function getChildrenCost()
    {
        return [
            0 => ['data-value' => 0],
            1 => ['data-value' => $this->year->COST_OF_CHILDREN * 1],
            2 => ['data-value' => $this->year->COST_OF_CHILDREN * 2],
            3 => ['data-value' => $this->year->COST_OF_CHILDREN * 3],
            4 => ['data-value' => $this->year->COST_OF_CHILDREN * 4],
            5 => ['data-value' => $this->year->COST_OF_CHILDREN * 5],
            6 => ['data-value' => $this->year->COST_OF_CHILDREN * 6],
        ];
    }

    public function getTotalInYear($field = 'total_salary')
    {
        $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->year->start) . ' 00:00:00');
        $endTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->year->end) . ' 23:59:59');

        $query = SalaryPeriodItems::find()
            ->andWhere(['user_id' => $this->user_id])
            ->byYear($startTime, $endTime)
            ->bySalary();

        switch ($field) {
            case "total_salary":
                return $query->sum('total_salary');
            case "tax":
                return $query->sum('tax');
            case "insurance":
                return $query->sum('insurance');
        }
    }

    /**
     * @return int
     * @throws \yii\web\NotFoundHttpException
     * مانده مساعده تا امروز
     */
    public function getAdvanceMoneyUntilThisMonth()
    {
        return (int)BalanceDaily::getBalanceDaily(Settings::get('m_debtor_advance_money'), $this->user->customer->oneAccount->id, Yii::$app->jdate->date("Y/m/d", $this->period->end_date));
    }


    public function getHoursOfOvertimeCost()
    {
        $basic_salary_hours = (float)$this->basic_salary / 7.33;
        return (int)($this->hours_of_overtime * $this->year->COST_HOURS_OVERTIME * $basic_salary_hours);
    }

    public function getHolidayOfOvertimeCost()
    {
        $basic_salary_hours = (float)$this->basic_salary / 7.33;
        return (int)($this->holiday_of_overtime * $this->year->COST_HOLIDAY_OVERTIME * $basic_salary_hours);
    }

    public function getNightOfOvertimeCost()
    {
        $basic_salary_hours = (float)$this->basic_salary / 7.33;
        return (int)($this->night_of_overtime * $this->year->COST_NIGHT_OVERTIME * $basic_salary_hours);
    }

    public function getHoursOfLowTimeCost()
    {
        $basic_salary_hours = (float)$this->basic_salary / 7.33;
        return (int)($this->hoursOfLowTime * $this->year->COST_HOURS_LOW_TIME * $basic_salary_hours);
    }

    public function getLastSalaryThisYear()
    {
        $lastSalaryThisYear = SalaryPeriodItems::find()
            ->joinWith(['period'])
            ->andWhere(['user_id' => $this->user_id])
            ->andWhere(['<=', 'end_date', $this->period->end_date])
            ->andWhere(['>', 'basic_salary', 0])
            ->bySalary()
            ->orderBy(['end_date' => SORT_DESC])
            ->one();
        return $lastSalaryThisYear !== null ? $lastSalaryThisYear->basic_salary : 0;
    }

    public function getContWorkThisYear()
    {
        return (int)SalaryPeriodItems::find()
            ->andWhere(['user_id' => $this->user_id])
            ->bySalary()
            ->byYear($this->period->start_date, $this->period->end_date)
            ->sum('hours_of_work');
    }

    /**
     * سنوات دوره قبل
     */
    public function setYearPaymentBeforeThisPeriod()
    {
        $this->yearCountThisPeriod = $this->getContWorkThisYear();
        $this->yearAmountThisPeriod = (int)($this->basic_salary * 30 * $this->yearCountThisPeriod / $this->year->countDay);
        return $this->save(false);
    }

    /**
     * @return int
     */
    public function getFinalPayment(): int
    {
        return (int)($this->payment_salary - $this->advance_money - $this->insurance_addition - (int)$this->non_cash_commission);
    }

    /**
     * @return string
     */
    public static function getFinalPaymentStringAttributes(): string
    {
        return 'payment_salary - advance_money - insurance_addition - non_cash_commission';
    }

    /**
     * {@inheritdoc}
     * @return SalaryPeriodItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SalaryPeriodItemsQuery(get_called_class());
    }

    public function canUpdate()
    {
        return $this->period->status == SalaryPeriod::STATUS_WAIT_CONFIRM;
    }

    public function canUpdateAfterConfirm()
    {
        return $this->period->status == SalaryPeriod::STATUS_CONFIRM || $this->period->status == SalaryPeriod::STATUS_PAYMENT;
    }

    public function canDelete()
    {
        return $this->period->status == SalaryPeriod::STATUS_WAIT_CONFIRM;
    }


    public function canPrint()
    {
        return $this->can_payment && $this->period->kind == SalaryPeriod::KIND_SALARY;
    }

    public function loadDefaultValuesBeforeCreate()
    {
        $this->setHistoryOfWork();
        $this->hours_of_work = $this->period->countDay;
        $this->treatment_day = 0;

        $this->cost_of_trust = 0;
        $this->non_cash_commission = 0;

        $this->count_of_children = 0;
        $this->basic_salary = (int)$this->year->MIN_BASIC_SALARY;
        if (($oldModel = self::find()->andWhere(['user_id' => $this->user_id])->bySalary()->orderBy(['period_id' => SORT_DESC])->one()) !== null && $oldModel->basic_salary > 0) {
            if ($this->year->id == $oldModel->year->id) {
                $this->basic_salary = $oldModel->basic_salary;
            }
            $this->count_of_children = $oldModel->count_of_children;
            $this->description = $oldModel->description;
        }
        $this->setSalaryItemsAddition();
        $this->cost_of_children = $this->count_of_children * $this->year->COST_OF_CHILDREN;
        $this->count_point = UserPoints::countRequestPayment($this->user_id);
        $this->cost_point = $this->count_point * $this->year->COST_USER_POINTS;
        $this->setCostOfFood();
        $this->setCostOfHouse();
        $this->setCostOfSpouse();
        $this->setCostOfYear();
        $this->beforeCreateOrUpdate();
    }

    /**
     * @return void
     */
    public function beforeCreateOrUpdate(): void
    {
        $this->advance_money = $this->advanceMoneyUntilThisMonth;
        if ($this->advance_money < 0) {
            $this->advance_money = 0;
        }
        $this->insurance_addition = (int)$this->employee->count_insurance_addition * (int)$this->year->COST_INSURANCE_ADDITION;
    }

    public function loadDefaultValuesBeforeCopy()
    {
        $this->setHistoryOfWork();
        $this->hours_of_work = $this->period->countDay;
        $this->treatment_day = 0;
        $this->count_of_children = 0;
        $this->basic_salary = (int)$this->year->MIN_BASIC_SALARY;
        if (($oldModel = self::find()->andWhere(['user_id' => $this->user_id])->bySalary()->orderBy(['period_id' => SORT_DESC])->limit(1)->one()) !== null && $oldModel->basic_salary > 0) {
            $this->basic_salary = $oldModel->basic_salary;
            $this->count_of_children = $oldModel->count_of_children;


            $this->cost_of_year = $oldModel->cost_of_year;
            $this->cost_of_trust = $oldModel->cost_of_trust;
            $this->cost_of_children = $oldModel->cost_of_children;
            $this->description = $oldModel->description;
            if ($oldModel->hours_of_work < $this->period->countDay) {
                $this->hours_of_work = $oldModel->hours_of_work;
            }

            if ($this->hours_of_work == $this->period->countDay) {
                $this->cost_of_food = $this->year->COST_OF_FOOD;
                $this->cost_of_house = $this->year->COST_OF_HOUSE;
                $this->cost_of_spouse = $this->employee->marital == EmployeeBranchUser::MARITAL_MARRIED ? (int)$this->year->COST_OF_SPOUSE : 0;
            } else {
                $this->cost_of_food = ($this->year->COST_OF_FOOD / $this->period->countDay * $this->hours_of_work);
                $this->cost_of_house = ($this->year->COST_OF_HOUSE / $this->period->countDay * $this->hours_of_work);
                $this->cost_of_spouse = $this->employee->marital == EmployeeBranchUser::MARITAL_MARRIED ? ((int)$this->year->COST_OF_SPOUSE / $this->period->countDay * $this->hours_of_work) : 0;
            }
            if ($this->historyOfWork > $this->year->countDay) {
                $this->rate_of_year = $this->hours_of_work == $this->period->countDay ? $this->year->COST_OF_YEAR : (int)($this->year->COST_OF_YEAR * $this->hours_of_work / $this->period->countDay);
            }
        }
        $this->setSalaryItemsAddition();
        $this->count_point = 0;
        $this->beforeCreateOrUpdate();
        $basic_salary_hours = (float)$this->basic_salary / 7.33;
        $this->total_salary = ($this->hours_of_work * $this->basic_salary) + $this->cost_of_trust + $this->cost_of_food + $this->cost_of_house + $this->rate_of_year + $this->commission;

        if ($this->hours_of_overtime > 0) {
            $this->total_salary += (int)($this->hours_of_overtime * $this->year->COST_HOURS_OVERTIME * $basic_salary_hours);
        }
        if ($this->hoursOfLowTime > 0) {
            $this->total_salary -= (int)($this->hoursOfLowTime * $this->year->COST_HOURS_LOW_TIME * $basic_salary_hours);
        }
        if ($this->holiday_of_overtime > 0) {
            $this->total_salary += (int)($this->holiday_of_overtime * $this->year->COST_HOLIDAY_OVERTIME * $basic_salary_hours);
        }
        if ($this->night_of_overtime > 0) {
            $this->total_salary += (int)($this->night_of_overtime * $this->year->COST_NIGHT_OVERTIME * $basic_salary_hours);
        }

        $this->cost_point = 0;
        $this->total_salary += $this->cost_of_spouse + $this->cost_point;

        if ($this->employee->manager) {
            // عضو هیات مدیره
            $this->insurance = (int)(($this->hours_of_work * $this->basic_salary) * $this->year->COST_INSURANCE_MANAGER); // بیمه
            $this->insurance_owner = (int)(($this->hours_of_work * $this->basic_salary) * $this->year->COST_INSURANCE_OWNER_MANAGER); // بیمه کارفرما

        } else {
            $this->insurance = (int)($this->total_salary * $this->year->COST_INSURANCE); // بیمه
            $this->insurance_owner = (int)($this->total_salary * $this->year->COST_INSURANCE_OWNER); // بیمه کارفرما
        }
        $this->total_salary += $this->cost_of_children; // حق اولاد مالیات دارد بیمه ندارد
        $this->tax = $this->calculateTaxSalary($this->total_salary - (int)($this->insurance * $this->year->getIMMUNITYINSURANCE()));
        $this->payment_salary = $this->total_salary - $this->insurance - $this->tax; // خالص حقوق
    }

    /**
     * @param $endDate
     * @param Year $year
     */
    public function loadDefaultValuesBeforeCreateReward($endDate, $year)
    {

        if ($this->employee->start_work > $year->start) {
            $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->employee->start_work) . ' 00:00:00');
        } else {
            $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($year->start) . ' 00:00:00');
        }
        $endTime = strtotime(Jdf::Convert_jalali_to_gregorian($year->end) . ' 23:59:59');

        $this->hours_of_work = SalaryPeriodItems::find()
                ->andWhere(['user_id' => $this->user_id])
                ->bySalary()
                ->byYear($startTime, $endTime)
                ->sum('hours_of_work') +
            (int)SalaryPeriodItems::find()
                ->andWhere(['user_id' => $this->user_id])
                ->byYear($startTime, $endTime)
                ->sum('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.treatment_day")');

        $lastSalaryThisYear = SalaryPeriodItems::find()
            ->joinWith(['period'])
            ->andWhere(['user_id' => $this->user_id])
            ->andWhere(['<=', 'end_date', $endDate])
            ->andWhere(['>', 'basic_salary', 0])
            ->bySalary()
            ->orderBy(['end_date' => SORT_DESC])
            ->one();

        $yearCountDay = $year->countDay;
        $newTotalSalary = $lastSalaryThisYear->basic_salary * 60 * $this->hours_of_work / $yearCountDay;
        $maxTotalSalary = $year->MIN_BASIC_SALARY * 90 * $this->hours_of_work / $yearCountDay;
        if ($newTotalSalary > $maxTotalSalary) {
            $this->total_salary = (int)($year->MIN_BASIC_SALARY * 90 * $this->hours_of_work / $yearCountDay);
            $this->basic_salary = (int)($this->total_salary / 60 * $yearCountDay / $this->hours_of_work);
        } else {
            $this->total_salary = (int)($lastSalaryThisYear->basic_salary * 60 * $this->hours_of_work / $yearCountDay);
            $this->basic_salary = (int)($lastSalaryThisYear->basic_salary);
        }

//        if ($this->hours_of_work < $yearCountDay) {
//            // اگر کارمند کمتر از ۳۶۵ روز کارکزد داشت
//            // عیدی با ۳۶۵ روز کامل محاسبه میکنیم به تناسب روز کارکرد بهش عیدی میدهیم
//            $newTotalSalary = $lastSalaryThisYear->basic_salary * 60;
//            $maxTotalSalary = $year->MIN_BASIC_SALARY * 90;
//            if ($newTotalSalary > $maxTotalSalary) {
//                $total_salary_full = $year->MIN_BASIC_SALARY * 90;
//            } else {
//                $total_salary_full = $lastSalaryThisYear->basic_salary * 60;
//            }
//
//            $this->total_salary = (int)($total_salary_full * $this->hours_of_work / $yearCountDay);
//        }

        if ($this->employee->end_work) {
            $this->advance_money = BalanceDetailed::getBalance(Settings::get('m_debtor_advance_money'), $this->employee->account_id);
        } else {
            $this->advance_money = 0;
        }

    }


    /**
     * @param $endDate
     * @param Year $year
     */
    public function loadDefaultValuesBeforeCreateYear($endDate, $year)
    {

        if ($this->employee->start_work > $year->start) {
            $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->employee->start_work) . ' 00:00:00');
        } else {
            $startTime = 0;
        }
        $endTime = strtotime(Jdf::Convert_jalali_to_gregorian($year->end) . ' 23:59:59');

        $this->hours_of_work = SalaryPeriodItems::find()
                ->andWhere(['user_id' => $this->user_id])
                ->bySalary()
                ->byYear($startTime, $endTime)
                ->sum('hours_of_work') +
            (int)SalaryPeriodItems::find()
                ->andWhere(['user_id' => $this->user_id])
                ->byYear($startTime, $endTime)
                ->sum('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.treatment_day")');

        if ($this->employee !== null && $this->employee->work_history_day_count > 0) {
            $this->hours_of_work += (int)$this->employee->work_history_day_count;
        }

        if (($lastSalaryThisYear = self::find()
                ->andWhere(['user_id' => $this->user_id])
                ->lastPayment($endDate)
                ->one()) !== null) {

            $this->basic_salary = $lastSalaryThisYear->basic_salary;
            $this->payment_salary = $this->total_salary = (int)($this->basic_salary * 30 * $this->hours_of_work / $year->countDay);
        }

    }


    public function loadDefaultValuesBeforeUpdate()
    {
        $this->setHistoryOfWork();
        $this->count_point = 0;
        $this->cost_point = 0;
        $this->setSalaryItemsAddition('update');
        $this->beforeCreateOrUpdate();
    }

    public function loadDefaultValuesBeforeUpdateReward()
    {

    }

    public function loadDefaultValuesBeforeUpdateYear()
    {

    }

    public function setCostOfFood()
    {
        $this->cost_of_food = $this->year->COST_OF_FOOD;
    }

    public function setCostOfHouse()
    {
        $this->cost_of_house = $this->year->COST_OF_HOUSE;
    }

    public function setCostOfSpouse()
    {
        $this->cost_of_spouse = $this->employee->marital == EmployeeBranchUser::MARITAL_MARRIED ? (int)$this->year->COST_OF_SPOUSE : 0;
    }

    public function setCostOfOvertime()
    {
        $this->cost_hours_of_overtime = 0;
        if ($this->hours_of_overtime > 0) {
            $calculate = (int)(($this->basic_salary * $this->hours_of_work) + $this->cost_of_trust + $this->cost_of_food + $this->cost_of_house + $this->cost_of_spouse + $this->cost_of_children + $this->rate_of_year + $this->cost_point);
            if ($this->total_salary > $calculate) {
                $this->cost_hours_of_overtime = $this->total_salary - $calculate;
            }
        }
    }

    public function setCostOfYear()
    {
        if ($this->historyOfWorkConvertToYear > 0) {
            $this->cost_of_year = $this->getRateOfYearFromHistory();
            $this->rate_of_year = $this->getRateOfYearFromHistory();
        } else {
            $this->cost_of_year = 0;
            $this->rate_of_year = 0;
        }

    }

    public function variance()
    {
        $calculate = (int)(($this->basic_salary * $this->hours_of_work) + $this->cost_of_trust + $this->cost_of_food + $this->cost_of_house + $this->cost_of_spouse + $this->cost_of_children + $this->rate_of_year + $this->cost_hours_of_overtime + $this->cost_point);
        if ($this->total_salary != $calculate) {
            return ($this->total_salary - $calculate);
        }
    }

    public function getTotalPaymentSalaryInYear()
    {
        return (int)SalaryPeriodItems::find()->andWhere(['user_id' => $this->user_id])->sum('payment_salary');
    }

    public function sendSmsPayment()
    {
        $total_salary = number_format((float)($this->payment_salary - $this->advance_money));

        $message = "<آوا پرداز>";
        $message .= "\n\r";
        $message .= $this->user->fullName . ' عزیز';
        $message .= "\n\r";
        $message .= "مبلغ {$total_salary} ریال بابت حقوق {$this->period->title}  به حساب شما واریز گردید.";
        if ($this->count_point > 0) {
            $cost_point = number_format((float)$this->cost_point);
            $message .= "\n\r";
            $message .= "مبلغ {$cost_point} ریال ({$this->count_point}امتیاز) بابت امتیازات شما می باشد.";
        }
        if ($this->advance_money > 0) {
            $advance_money = number_format((float)$this->advance_money);
            $message .= "\n\r";
            $message .= "مبلغ {$advance_money} ریال بابت مساعده کسر گردید.";
        }
        $message .= "\n\r";

        Yii::$app->avaQueue->push(new SmsArrayJob([
            'receptors' => $this->user->username,
            'messages' => $message,
            'model_class' => self::class,
            'model_id' => $this->getPrimaryKey() ?? null
        ]));
    }

    /**
     * @param string $action
     * بر اساس اطلاعات جدول اضافات و کسورات حقوق
     */
    public function setSalaryItemsAddition($action = 'create')
    {
        /** مرخصی بدون حقوق */
        $this->detailAddition['countOfDayLeaveNoSalary'] = [];
        $oldCountOfDayLeaveNoSalary = (int)$this->countOfDayLeaveNoSalary;
        $this->countOfDayLeaveNoSalary = 0;
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_LEAVE_DAILY, SalaryItemsAddition::TYPE_LEAVE_NO_SALARY_DAILY, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['countOfDayLeaveNoSalary'][] = $item->title;
            $this->countOfDayLeaveNoSalary += $item->convertValueToDay;
        }
        if ($action == 'create' && $this->countOfDayLeaveNoSalary > 0) {
            $this->hours_of_work -= $this->countOfDayLeaveNoSalary;
        }
        if ($action == 'update' && ($oldCountOfDayLeaveNoSalary - $this->countOfDayLeaveNoSalary) !== 0) {
            $this->hours_of_work += ($oldCountOfDayLeaveNoSalary - $this->countOfDayLeaveNoSalary);
        }


        /** مرخصی استعلاجی */
        $this->detailAddition['treatment_day'] = [];
        $this->treatment_day = 0;
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_LEAVE_DAILY, SalaryItemsAddition::TYPE_LEAVE_TREATMENT_DAILY, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['treatment_day'][] = $item->title;
            $this->treatment_day += $item->convertValueToDay;
        }

        /** اضافه کاری */
        $this->detailAddition['hours_of_overtime'] = [];
        $this->hours_of_overtime = 0;
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_OVER_TIME, SalaryItemsAddition::TYPE_OVER_TIME_DAY, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['hours_of_overtime'][] = $item->title;
            $this->hours_of_overtime += $item->convertValueToHour;
        }


        /** تعطیل کاری */
        $this->detailAddition['holiday_of_overtime'] = [];
        $this->holiday_of_overtime = 0;
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_OVER_TIME, SalaryItemsAddition::TYPE_OVER_TIME_HOLIDAY, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['holiday_of_overtime'][] = $item->title;
            $this->holiday_of_overtime += $item->convertValueToHour;
        }
        if ($this->period->year->CALCULATE_EMPLOYEE_DAY && Yii::$app->jdate->date("m", $this->period->start_date) == '02') {
            //ماه اردیبهشت . روز کارمند - محاسبه تعطیا کاری
            $this->detailAddition['holiday_of_overtime'][] = "مزد تعطیل کاری روز کارمند";
            $this->holiday_of_overtime += 7.33;
        }

        /** شب کاری */
        $this->detailAddition['night_of_overtime'] = [];
        $this->night_of_overtime = 0;
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_OVER_TIME, SalaryItemsAddition::TYPE_OVER_TIME_NIGHT, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['night_of_overtime'][] = $item->title;
            $this->night_of_overtime += $item->convertValueToHour;
        }

        /** کسر کار */
        $this->detailAddition['hoursOfLowTime'] = [];
        $this->hoursOfLowTime = -2; // تا دوساعت در ماه مجاز
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_LOW_TIME, null, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['hoursOfLowTime'][] = $item->title;
            $this->hoursOfLowTime += $item->convertValueToHour;
        }


        if ($this->hoursOfLowTime < 0) {
            $this->detailAddition['hoursOfLowTime'] = [];
            $this->hoursOfLowTime = 0;
        }

        if ($this->hours_of_overtime > 0 && $this->hoursOfLowTime > 0) {
            if ($this->hours_of_overtime >= $this->hoursOfLowTime) {
                $this->hours_of_overtime -= $this->hoursOfLowTime;
                $this->hoursOfLowTime = 0;
            } else {
                $this->hoursOfLowTime -= $this->hours_of_overtime;
                $this->hours_of_overtime = 0;
            }
        }

        /** پورسانت */
        $this->detailAddition['commission'] = [];
        $this->commission = 0;
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_COMMISSION, null, $this->period->start_date, $this->period->end_date)->all() as $item) {
            $this->detailAddition['commission'][] = $item->title;
            $this->commission += $item->convertValueToAmount;
        }
        foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_COMMISSION_CONST, null, null, null)->all() as $item) {
            $this->detailAddition['commission'][] = $item->title;
            $this->commission += $item->convertValueToAmount;
        }
//        foreach (ComfortItems::find()->byUser($this->user_id)->byDate($this->period->start_date, strtotime("+1 DAY", $this->period->end_date))->byStatus(ComfortItems::STATUS_CONFIRM)->all() as $item) {
//            $this->detailAddition['commission'][] = "امکان رفاهی " . "(" . $item->comfort->title . ")";
//            $this->commission += $item->amount;
//        }

        if (Yii::$app->jdate->date("Y/m/d", $this->period->end_date) > '1403/03/31') {
            /** مزایای غیر نقدی */
            $this->detailAddition['non_cash_commission'] = [];
            $this->non_cash_commission = 0;
            foreach (SalaryItemsAddition::find()->bySalary($this->user_id, SalaryItemsAddition::KIND_NON_CASH, null, $this->period->start_date, $this->period->end_date)->all() as $item) {
                $this->detailAddition['non_cash_commission'][] = $item->title;
                $this->non_cash_commission += $item->convertValueToAmount;
            }
        }
    }

    public static function itemAlias($type, $code = NULL, $addData = false)
    {

        $_items = [
            'Children' => [
                0 => 'بدون فرزند',
                1 => 'یک فرزند',
                2 => 'دو فرزند',
                3 => 'سه فرزند',
                4 => 'چهار فرزند',
                5 => 'پنج فرزند',
                6 => 'شش فرزند',
            ],
            'HintLabel' => [
                'hours_of_work' => 'تعداد روز کارکرد',
                'basic_salary' => 'دستمزد روزانه',
                'cost_of_house' => 'حق مسکن',
                'cost_of_food' => 'حق بن و خوارو بار',
                'cost_of_children' => 'حق اولاد (عائله مندی)',
                'rate_of_year' => 'نرخ پایه (سنوات) کارگران مشمول طرح‌های طبقه بندی مشاغل',
                'hours_of_overtime' => 'تعداد ساعات اضافه کاری',
                'holiday_of_overtime' => 'تعداد ساعات تعطیل کاری',
                'night_of_overtime' => 'تعداد ساعات شب کاری',
                'total_salary' => 'مجموع ناخالص حقوق و مزایا',
                'payment_salary' => 'مجموع خالص حقوق و مزایا',
                'count_point' => 'تعداد امتیازات',
                'final_payment' => 'پرداختی نهایی به کاربر',
                'treatment_day' => 'تعداد روز استعلاجی',
                'hoursOfLowTime' => 'تعداد ساعات کسر کار',
                'countOfDayLeaveNoSalary' => 'تعداد روز مرخصی بدون حقوق',
            ]
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }


    public function calculateTaxSalary($salary)
    {
        $tax = 0;
        $cost_tax_step_1 = $this->year->COST_TAX_STEP_1_MIN * $this->hours_of_work / $this->period->countDay;
        $cost_tax_step_2 = $this->year->COST_TAX_STEP_2_MIN * $this->hours_of_work / $this->period->countDay;
        $cost_tax_step_3 = $this->year->COST_TAX_STEP_3_MIN * $this->hours_of_work / $this->period->countDay;
        $cost_tax_step_4 = $this->year->COST_TAX_STEP_4_MIN * $this->hours_of_work / $this->period->countDay;

        if ($salary > $cost_tax_step_1) {
            $step_1_salary = min(($salary - $cost_tax_step_1), ($cost_tax_step_2 - $cost_tax_step_1));
            $tax += (int)($step_1_salary * $this->year->COST_TAX_STEP_1_PERCENT);
        }

        if ($salary > $cost_tax_step_2) {
            $step_2_salary = min(($salary - $cost_tax_step_2), ($cost_tax_step_3 - $cost_tax_step_2));
            $tax += (int)($step_2_salary * $this->year->COST_TAX_STEP_2_PERCENT);
        }

        if ($salary > $cost_tax_step_3) {
            $step_3_salary = min(($salary - $cost_tax_step_3), ($cost_tax_step_4 - $cost_tax_step_3));
            $tax += (int)($step_3_salary * $this->year->COST_TAX_STEP_3_PERCENT);
        }

        if ($salary > $this->year->COST_TAX_STEP_4_MIN) {
            $step_4_salary = $salary - $this->year->COST_TAX_STEP_4_MIN;
            $tax += (int)($step_4_salary * $this->year->COST_TAX_STEP_4_PERCENT);
        }

        return $tax;
    }

    public function calculateTaxReward($reward)
    {
        $tax = 0;
        $finalTax = 0;
        $cost_tax_step_1 = $this->year->COST_TAX_REWARD_STEP_1_MIN * $this->hours_of_work / $this->year->countDay;
        $cost_tax_step_2 = $this->year->COST_TAX_REWARD_STEP_2_MIN * $this->hours_of_work / $this->year->countDay;
        $cost_tax_step_3 = $this->year->COST_TAX_REWARD_STEP_3_MIN * $this->hours_of_work / $this->year->countDay;
        $cost_tax_step_4 = $this->year->COST_TAX_REWARD_STEP_4_MIN * $this->hours_of_work / $this->year->countDay;
        if ($reward > 0) {
            if ($reward > $cost_tax_step_1) {
                $step_1_salary = min(($reward - $cost_tax_step_1), ($cost_tax_step_2 - $cost_tax_step_1));
                $tax += (int)($step_1_salary * $this->year->COST_TAX_STEP_1_PERCENT);
            }

            if ($reward > $cost_tax_step_2) {
                $step_2_salary = min(($reward - $cost_tax_step_2), ($cost_tax_step_3 - $cost_tax_step_2));
                $tax += (int)($step_2_salary * $this->year->COST_TAX_STEP_2_PERCENT);
            }

            if ($reward > $cost_tax_step_3) {
                $step_3_salary = min(($reward - $cost_tax_step_3), ($cost_tax_step_4 - $cost_tax_step_3));
                $tax += (int)($step_3_salary * $this->year->COST_TAX_STEP_3_PERCENT);
            }

            if ($reward > $cost_tax_step_4) {
                $step_4_salary = $reward - $cost_tax_step_4;
                $tax += (int)($step_4_salary * $this->year->COST_TAX_STEP_4_PERCENT);
            }

            if (($c = $tax - $this->getTotalInYear('tax')) > 0) {
                $finalTax = $c;
            }
        }

        return $finalTax;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
        }
        if (in_array($this->getScenario(), [self::SCENARIO_CREATE, self::SCENARIO_UPDATE])) {
            $this->cost_of_children = $this->count_of_children * $this->year->COST_OF_CHILDREN;
            if (($this->hours_of_work + (int)$this->treatment_day) < $this->period->countDay) {
                $this->cost_of_children = $this->cost_of_children / $this->period->countDay * ($this->hours_of_work + (int)$this->treatment_day);
            }
            $total_salary = ($this->total_salary - $this->cost_of_children - (int)$this->non_cash_commission);// حق اولاد مالیات دارد بیمه ندارد
            if ($this->employee->manager) {
                // عضو هیات مدیره
                $this->insurance = (int)(($this->hours_of_work * $this->basic_salary) * $this->year->COST_INSURANCE_MANAGER); // بیمه
                $this->insurance_owner = (int)(($this->hours_of_work * $this->basic_salary) * $this->year->COST_INSURANCE_OWNER_MANAGER); // بیمه کارفرما
            } else {
                $this->insurance = (int)($total_salary * $this->year->COST_INSURANCE); // بیمه
                $this->insurance_owner = (int)($total_salary * $this->year->COST_INSURANCE_OWNER); // بیمه کارفرما
            }
            $this->tax = $this->calculateTaxSalary($this->total_salary - (int)$this->non_cash_commission - (int)($this->insurance * $this->year->getIMMUNITYINSURANCE()));
            $this->cost_point = (int)$this->count_point * (int)$this->year->COST_USER_POINTS;
        }
        if (in_array($this->getScenario(), [self::SCENARIO_CREATE_REWARD, self::SCENARIO_UPDATE_REWARD])) {
            $this->tax = $this->calculateTaxReward($this->getTotalInYear() + $this->total_salary - ($this->year->COST_TAX_STEP_1_MIN) - ((int)$this->getTotalInYear('insurance') * $this->year->getIMMUNITYINSURANCE()));
            $this->payment_salary = (int)($this->total_salary - $this->tax);
        }

        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
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
                'ownerClassName' => self::class,
                'saveAfterInsert' => true,
                'savePostDataAfterInsert' => true,
                'savePostDataAfterUpdate' => true,
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'count_point' => 'Integer',
                    'cost_point' => 'Integer',
                    'salary_decrease' => 'Integer',
                    'treatment_day' => 'Integer',
                    'description' => 'String',
                    'yearPaymentBeforeThisPeriod' => 'Integer',
                    'yearCountThisPeriod' => 'Integer',
                    'yearAmountThisPeriod' => 'Integer',
                    'countOfDayLeaveNoSalary' => 'Integer',
                    'hoursOfLowTime' => 'Float',
                    'hoursOfLowTimeCost' => 'Integer',
                    'detailAddition' => 'Any',
                    'descriptionShowEmployee' => 'String',
                ],

            ],
        ];
    }

}
