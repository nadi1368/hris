<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\validators\DateValidator;
use hesabro\helpers\validators\IBANValidator;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_salary_period}}".
 *
 * @property int $id
 * @property int $workshop_id
 * @property string $title
 * @property int $start_date
 * @property int $end_date
 * @property array $additional_data
 * @property int $status
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 *
 * @property SalaryPeriodItems[] $salaryPeriodItems
 * @property WorkshopInsurance $workshop
 * @property int $countDay
 * @property string $fullName
 * @property string $titleWitYear
 */
class SalaryPeriodBase extends \yii\db\ActiveRecord
{
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_PAYMENT = 3;

    const KIND_SALARY = 0; // حقوق
    const KIND_REWARD = 1; // عید
    const KIND_YEAR = 2; // سنوات

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CREATE_REWARD = 'create_reward';
    const SCENARIO_CREATE_YEAR = 'create_year';
    const SCENARIO_EXPORT = "export"; //خروجی بانک
    const SCENARIO_EXPORT_NONE_CASH = "export_non_cash"; //خروجی بانک
    const SCENARIO_PAYMENT = "payment"; // پرداخت
    const SCENARIO_EXPORT_INSURANCE = "insurance"; // خروجی بیمه

    public $disableStartDate = false;

    public $error_msq = '';


    public $shaba = '', $bank_name = '', $file_number = 0, $payment_date, $another_period;

    /** additional_data */
    public $DSK_KIND, $DSK_LISTNO, $DSK_DISC, $DSK_NUM, $DSK_TDD, $DSK_TROOZ, $DSK_TMAH, $DSK_TMAZ, $DSK_TMASH, $DSK_TTOTL, $DSK_TBIME, $DSK_TKOSO, $DSK_BIC, $DSK_RATE, $DSK_PRATE, $DSK_BIMH;
    public $sms_payment = 0;
    public $kind = 0;
    public $setRollCall;

    /**f
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_salary_period}}';
    }

    public function beforeValidate()
    {
        if (in_array($this->getScenario(), [self::SCENARIO_CREATE]) && strlen($this->start_date) == 7) {
            $this->start_date .= '/01';
        }
        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['workshop_id', 'title', 'start_date'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['workshop_id', 'title'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [['start_date', 'end_date'], DateValidator::class, 'on' => [self::SCENARIO_CREATE]],
            ['start_date', 'validateStartDate', 'skipOnError' => false, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREATE]],
            [['creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['workshop_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkshopInsurance::class, 'targetAttribute' => ['workshop_id' => 'id']],
            [['shaba', 'bank_name', 'file_number'], 'required', 'on' => [self::SCENARIO_EXPORT]],
            [['another_period'], 'integer', 'on' => [self::SCENARIO_EXPORT_NONE_CASH]],
            [['payment_date'], 'required', 'on' => [self::SCENARIO_PAYMENT]],
            [['payment_date'], DateValidator::class, 'on' => [self::SCENARIO_PAYMENT]],
            [['shaba'], IBANValidator::class, 'on' => [self::SCENARIO_EXPORT]],
            [['DSK_KIND', 'DSK_LISTNO', 'DSK_DISC', 'DSK_NUM', 'DSK_TDD', 'DSK_TROOZ', 'DSK_TMAH', 'DSK_TMAZ', 'DSK_TMASH', 'DSK_TTOTL', 'DSK_TBIME', 'DSK_TKOSO', 'DSK_BIC', 'DSK_RATE', 'DSK_PRATE', 'DSK_BIMH'], 'required', 'on' => [self::SCENARIO_EXPORT_INSURANCE]],
            [['DSK_LISTNO', 'DSK_DISC', 'DSK_NUM', 'DSK_TDD', 'DSK_TROOZ', 'DSK_TMAH', 'DSK_TMAZ', 'DSK_TMASH', 'DSK_TTOTL', 'DSK_TBIME', 'DSK_TKOSO', 'DSK_BIC', 'DSK_RATE', 'DSK_PRATE', 'DSK_BIMH'], 'string', 'on' => [self::SCENARIO_EXPORT_INSURANCE]],
            [['DSK_KIND'], 'integer', 'on' => [self::SCENARIO_EXPORT_INSURANCE]],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['title', 'start_date', '!end_date'];
        $scenarios[self::SCENARIO_UPDATE] = ['title'];
        $scenarios[self::SCENARIO_CREATE_REWARD] = ['title'];
        $scenarios[self::SCENARIO_CREATE_YEAR] = ['title'];
        $scenarios[self::SCENARIO_EXPORT] = ['shaba', 'bank_name', 'file_number', 'another_period'];
        $scenarios[self::SCENARIO_EXPORT_NONE_CASH] = ['another_period'];
        $scenarios[self::SCENARIO_PAYMENT] = ['payment_date'];
        $scenarios[self::SCENARIO_EXPORT_INSURANCE] = ['DSK_KIND', 'DSK_LISTNO', 'DSK_DISC', 'DSK_NUM', 'DSK_TDD', 'DSK_TROOZ', 'DSK_TMAH', 'DSK_TMAZ', 'DSK_TMASH', 'DSK_TTOTL', 'DSK_TBIME', 'DSK_TKOSO', 'DSK_BIC', 'DSK_RATE', 'DSK_PRATE', 'DSK_BIMH'];

        return $scenarios;
    }


    /**
     * @param $attribute
     * @param $params
     */
    public function validateStartDate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $start_time = strtotime(Jdf::Convert_jalali_to_gregorian($this->start_date));
            if (self::find()->andWhere(['start_date' => $start_time, 'workshop_id' => $this->workshop_id])->bySalary()->limit(1)->one() !== null) {
                $this->addError($attribute, 'برای تاریخ مد نظر قبلا دروه حقوق ایجاد شده است.');
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
            'workshop_id' => Yii::t('app', 'Workshop Insurances'),
            'title' => Yii::t('app', 'Title'),
            'month' => Yii::t('app', 'Date'),
            'start_date' => Yii::t('app', 'Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'created' => Yii::t('app', 'Created'),
            'changed' => Yii::t('app', 'Changed'),
            'status' => Yii::t('app', 'Status'),
            'kind' => Yii::t('app', 'Type'),
            'DSK_KIND' => 'نوع لیست',
            'DSK_LISTNO' => 'شماره لیست',
            'DSK_DISC' => 'شرح لیست',
            'DSK_NUM' => 'تعداد کارکنان',
            'DSK_TDD' => 'مجموع روز های کارکرد',
            'DSK_TROOZ' => 'مجموع دستمزد روزانه',
            'DSK_TMAH' => 'مجموع دستمزد ماهانه',
            'DSK_TMAZ' => 'مجموع مزایای ماهانه مشمول',
            'DSK_TMASH' => 'مجموع دستمزد مزایای ماهانه مشمول',
            'DSK_TTOTL' => 'مجموع کل مزایای  ماهانه (مشمولٍ غیر مشمول)',
            'DSK_TBIME' => 'مجموع حق بیمه کارمند',
            'DSK_TKOSO' => 'مجموع حق بیمه کارفرما',
            'DSK_BIC' => 'مجموع حق بیکاری',
            'DSK_RATE' => 'نرخ حق بیمه',
            'DSK_PRATE' => 'نرخ پورسانت',
            'DSK_BIMH' => 'نرخ مشاغل سخت و زیان',

            'payment_date' => 'تاریخ پرداخت',
            'another_period' => 'خروجی با دوره ی',
            'shaba' => 'شماره شبای بانک',
            'bank_name' => 'نام بانک',
            'file_number' => 'نام فایل',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalaryPeriodItems()
    {
        return $this->hasMany(SalaryPeriodItems::class, ['period_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkshop()
    {
        return $this->hasOne(WorkshopInsurance::class, ['id' => 'workshop_id']);
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

    public function getAnotherPeriodList()
    {
        return ArrayHelper::map(self::find()->andWhere(['<>', 'workshop_id', $this->workshop_id])->andWhere(['start_date' => $this->start_date])->byKind($this->kind)->groupBy(['workshop_id'])->all(), 'id', 'fullName');
    }

    public function getFullName()
    {
        return $this->workshop->title . ' - ' . $this->title;
    }

    public function getTitleWithYear()
    {
        return $this->title . ' - ' . Yii::$app->jdf->jdate("Y", $this->start_date);
    }

    /**
     * {@inheritdoc}
     * @return SalaryPeriodQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SalaryPeriodQuery(get_called_class());
    }

    public function canUpdate()
    {
        if ($this->status != self::STATUS_WAIT_CONFIRM) {
            return false;
        }
        if (self::find()->andWhere(['>', 'start_date', $this->start_date])->byKind($this->kind)->byWorkShop($this->workshop_id)->one() !== null) {
            return false;
        }
        if ($this->getSalaryPeriodItems()->one() !== null) {
            return false;
        }
        return true;
    }

    public function canDelete()
    {
        if ($this->status != self::STATUS_WAIT_CONFIRM) {
            return false;
        }
        if (self::find()->andWhere(['>', 'start_date', $this->start_date])->byKind($this->kind)->byWorkShop($this->workshop_id)->one() !== null) {
            return false;
        }
        if ($this->getSalaryPeriodItems()->one() !== null) {
            return false;
        }
        return true;
    }

    public function canDeleteItems()
    {
        if ($this->status != self::STATUS_WAIT_CONFIRM) {
            return false;
        }
        if (self::find()
                ->byKind($this->kind)
                ->andWhere(['>', 'start_date', $this->start_date])
                ->andWhere([SalaryPeriod::tableName() . '.workshop_id' => $this->workshop_id])
                ->one() !== null) {
            return false;
        }
        if ($this->getSalaryPeriodItems()->one() !== null) {
            return true;
        }

        return false;
    }

    public function canCopyPreviousPeriod()
    {
        if ($this->status != self::STATUS_WAIT_CONFIRM || ((int)Yii::$app->jdf->jdate("m", $this->start_date)) == 1) {
            return false;
        }
        if (self::find()->byPrevious($this->workshop_id, $this->start_date)->limit(1)->one() === null) {
            return false;
        }
        if ($this->getSalaryPeriodItems()->limit(1)->one() === null) {
            return true;
        }
        return false;
    }

    public function canConfirm()
    {
        $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->year->start) . ' 00:00:00');
        $endTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->year->end) . ' 23:59:59');
        if ($this->status != self::STATUS_WAIT_CONFIRM) {
            return false;
        }
//        if ($this->getSalaryPeriodItems()->limit(1)->one() === null) {
//            $this->error_msq = 'هیچ کارمندی به دوره حقوق اضافه نشده است.';
//            return false;
//        }
        if ($this->kind == self::KIND_SALARY) {
            if (($previousModel = self::find()->byPrevious($this->workshop_id, $this->start_date)->limit(1)->one()) !== null &&
                ($employeeLost = $previousModel->getSalaryPeriodItems()->joinWith(['employee'])->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")=""')->andWhere(['NOT IN', SalaryPeriodItems::tableName() . '.user_id', $this->getSalaryPeriodItems()->select(['user_id'])->andWhere(['OR', ['>', 'total_salary', 0], ['>', 'JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.treatment_day")', 0]])])->limit(1)->one()) !== null) {
                /** @var $employeeLost SalaryPeriodItems * */
                $this->error_msq = $employeeLost->user->fullName . " در دوره حقوق قبلی بوده است در این دوره اطلاعات آن ثبت نشده است.اگر ترک کار شده است لطفا ترک کار آن را ثبت کنید.";
                return false;
            }
            foreach ($this->getSalaryPeriodItems()->all() as $item) {
                /** @var $item SalaryPeriodItems * */
                if (!$item->employee->canCreateSalaryPayment()) {
                    $this->error_msq = $item->user->fullName . ': ' . $item->employee->error_msg;
                    return false;
                }
            }
        }
        if ($this->kind == self::KIND_REWARD) {
            if (($employeeLost = SalaryPeriodItems::find()->byWorkShop($this->workshop_id)->byYear($startTime, $endTime)->limit(1)->joinWith(['employee'])->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.checkout") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.checkout")=false')->andWhere(['NOT IN', SalaryPeriodItems::tableName() . '.user_id', $this->getSalaryPeriodItems()->select(['user_id'])->andWhere(['>', 'total_salary', 0])])->limit(1)->one()) !== null) {
                /** @var $employeeLost SalaryPeriodItems * */
                $this->error_msq = $employeeLost->user->fullName . " در این سال گردش حقوق دارد و باید به لیست اضافه شود.اگر تصویه حساب شده است لطفا تیک تسویه حساب آن را بزنید.";
                return false;
            }
            foreach ($this->getSalaryPeriodItems()->all() as $item) {
                /** @var $item SalaryPeriodItems * */
                if (!$item->employee->canCreateRewardPayment()) {
                    $this->error_msq = $item->user->fullName . ': ' . $item->employee->error_msg;
                    return false;
                }
            }
        }
        return true;
    }

    public function canPayment()
    {
        $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->year->start) . ' 00:00:00');
        $endTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->year->end) . ' 23:59:59');
        if ($this->status != self::STATUS_CONFIRM) {
            return false;
        }
        if ($this->kind == self::KIND_SALARY) {
            if (($previousModel = self::find()->byPrevious($this->workshop_id, $this->start_date)->limit(1)->one()) !== null &&
                ($employeeLost = $previousModel->getSalaryPeriodItems()->joinWith(['employee'])->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")=""')->andWhere(['NOT IN', SalaryPeriodItems::tableName() . '.user_id', $this->getSalaryPeriodItems()->select(['user_id'])->andWhere(['OR', ['>', 'total_salary', 0], ['>', 'JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.treatment_day")', 0]])])->limit(1)->one()) !== null) {
                /** @var $employeeLost SalaryPeriodItems * */
                $this->error_msq = $employeeLost->user->fullName . " در دوره حقوق قبلی بوده است در این دوره اطلاعات آن ثبت نشده است.اگر ترک کار شده است لطفا ترک کار آن را ثبت کنید.";
                return false;
            }

            foreach ($this->getSalaryPeriodItems()->all() as $item) {
                /** @var $item SalaryPeriodItems * */
                if (!$item->employee->canCreateSalaryPayment()) {
                    $this->error_msq = $item->user->fullName . ': ' . $item->employee->error_msg;
                    return false;
                }
            }
        }
        if ($this->kind == self::KIND_REWARD) {
            if (($employeeLost = SalaryPeriodItems::find()->byWorkShop($this->workshop_id)->byYear($startTime, $endTime)->limit(1)->joinWith(['employee'])->andWhere('JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.checkout") IS NULL OR JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.checkout")=false')->andWhere(['NOT IN', SalaryPeriodItems::tableName() . '.user_id', $this->getSalaryPeriodItems()->select(['user_id'])->andWhere(['>', 'total_salary', 0])])->limit(1)->one()) !== null) {
                /** @var $employeeLost SalaryPeriodItems * */
                $this->error_msq = $employeeLost->user->fullName . " در این سال گردش حقوق دارد و باید به لیست اضافه شود.اگر تصویه حساب شده است لطفا تیک تسویه حساب آن را بزنید.";
                return false;
            }
            foreach ($this->getSalaryPeriodItems()->all() as $item) {
                /** @var $item SalaryPeriodItems * */
                if (!$item->employee->canCreateRewardPayment()) {
                    $this->error_msq = $item->user->fullName . ': ' . $item->employee->error_msg;
                    return false;
                }
            }
        }
        return true;
    }

    public function canReturnConfirm()
    {
        if ($this->status != self::STATUS_CONFIRM) {
            return false;
        }
        $findOpenPeriod = SalaryPeriod::find()
            ->byKind($this->kind)
            ->andWhere(['>', 'start_date', $this->start_date])
            ->andWhere(['<>', SalaryPeriod::tableName() . '.status', SalaryPeriod::STATUS_PAYMENT])
            ->andWhere(['<>', SalaryPeriod::tableName() . '.id', $this->id])
            ->andWhere([SalaryPeriod::tableName() . '.workshop_id' => $this->workshop_id])
            ->limit(1)
            ->one();
        if ($findOpenPeriod !== null) {
            $this->error_msq = "برای برگشت به وضعیت در انتظار تایید باید وضعیت دوره  " . $findOpenPeriod->title . " در وضعیت پرداخت باشد. ";
            return false;
        }
        return true;
    }

    public function canReturnPayment()
    {
        if ($this->status != self::STATUS_PAYMENT) {
            return false;
        }
        $findOpenPeriod = SalaryPeriod::find()
            ->byKind($this->kind)
            ->andWhere(['>', 'start_date', $this->start_date])
            ->andWhere(['<>', SalaryPeriod::tableName() . '.status', SalaryPeriod::STATUS_PAYMENT])
            ->andWhere(['<>', SalaryPeriod::tableName() . '.id', $this->id])
            ->andWhere([SalaryPeriod::tableName() . '.workshop_id' => $this->workshop_id])
            ->limit(1)
            ->one();
        if ($findOpenPeriod !== null) {
            $this->error_msq = "برای برگشت به وضعیت تایید باید وضعیت دوره  " . $findOpenPeriod->title . " در وضعیت پرداخت باشد. ";
            return false;
        }
        return true;
    }

    public function canCreateItems()
    {
        if ($this->status == self::STATUS_WAIT_CONFIRM) {
            return true;
        }
        return false;
    }

    public function canSendPaymentSms()
    {
        return $this->status == self::STATUS_PAYMENT && $this->sms_payment == 0 && $this->end_date > strtotime('-10 DAY');
    }


    public function loadDefaultValuesBeforeCreate()
    {
        $this->setStartDate();
        $this->setEndDate();
        $this->title = Yii::$app->jdf->jdate("F", $this->start_date);
        $this->start_date = Yii::$app->jdf->jdate("Y/m", $this->start_date);
    }


    private function setStartDate()
    {
        if (($preModel = self::find()->andWhere(['workshop_id' => $this->workshop_id])->bySalary()->orderBy(['end_date' => SORT_DESC])->limit(1)->one()) !== null) {
            $this->start_date = strtotime('+1 DAY', $preModel->end_date);
        } else {
            $this->start_date = Jdf::getStartAndEndOfCurrentYear()['start'];
            $this->disableStartDate = true;
        }
    }

    public function setEndDate()
    {
        $start_date = strtotime(Jdf::Convert_jalali_to_gregorian($this->start_date));
        $year = Yii::$app->jdf->jdate("Y", $start_date);
        $month = Yii::$app->jdf->jdate("m", $start_date);
        $this->end_date = $year . '/' . $month . '/' . Jdf::lastDayInMonth($year, (int)$month);
    }

    public function loadDefaultValuesBeforeInsuranceExport($endWorkAll = false)
    {
        $this->DSK_KIND = $this->DSK_KIND ?: '';
        $this->DSK_LISTNO = $this->DSK_LISTNO ?: '01';
        $this->DSK_DISC = $this->DSK_DISC ?: '';
        $this->DSK_NUM = 0; // تعداد کارکنان
        $this->DSK_TDD = 0; //مجموع روز های کارکرد
        $this->DSK_TROOZ = 0; // مجموع دستمزد روزانه
        $this->DSK_TMAH = 0; // مجموع دستمزد ماهانه
        $totalInsuranceOwner = 0;
        $totalInsuranceOwnerManager = 0;
        $this->DSK_TMASH = 0;// مجموع دستمزد مزایای ماهانه مشمول
        $this->DSK_TTOTL = 0; // هجوَع کل مزایای  ماهانه (مشمولٍ غیر مشمول)
        $this->DSK_TBIME = 0; // مجموع حق بیمه کارمند
        if (!$endWorkAll) {
            foreach ($this->salaryPeriodItems as $item) {
                if ($item->employee->manager) {
                    $totalInsuranceOwnerManager += (int)$item->insurance_owner;
                    $this->DSK_TMASH += (int)($item->hours_of_work * $item->basic_salary);
                    $this->DSK_TTOTL += (int)($item->hours_of_work * $item->basic_salary);
                } else {
                    $totalInsuranceOwner += (int)$item->insurance_owner;
                    $this->DSK_TMASH += (int)($item->total_salary - $item->cost_of_children - $item->non_cash_commission);
                    $this->DSK_TTOTL += (int)($item->total_salary);
                }
                $this->DSK_TBIME += (int)$item->insurance;
                $this->DSK_TDD += (int)$item->hours_of_work;
                $this->DSK_TROOZ += (int)$item->basic_salary;
                $this->DSK_TMAH += (int)($item->hours_of_work * $item->basic_salary);
                $this->DSK_NUM++;
            }
        } else {
            $this->DSK_NUM = $this->getSalaryPeriodItems()->andWhere(['>', 'total_salary', 0])->count(); // تعداد کارکنان
        }
        $this->DSK_TMAZ = $endWorkAll ? 0 : $this->DSK_TMASH - $this->DSK_TMAH; // مجموع مزایای ماهانه مشمول
        $insurance_owner = $endWorkAll ? 0 : ($totalInsuranceOwnerManager + $totalInsuranceOwner); // مجموع حق بیمه کارفرما
        $this->DSK_TKOSO = $endWorkAll ? 0 : ($totalInsuranceOwner * 20 / 23) + $totalInsuranceOwnerManager; // 20 ٪ کارفرما // مجموع حق بیمه کارفرما
        $this->DSK_BIC = $endWorkAll ? 0 : ($insurance_owner - $this->DSK_TKOSO); // 3 ٪ بیکاری // مجموع حق بیکاری
        $this->DSK_RATE = $this->DSK_RATE ?: 0; // نرخ حق بیمه
        $this->DSK_PRATE = $this->DSK_PRATE ?: 0; //نرخ پورسانت
        $this->DSK_BIMH = $this->DSK_BIMH ?: 0; //نرخ مشاغل سخت و زیان
    }

    public function getCountDay()
    {
        return Jdf::lastDayInMonth(Yii::$app->jdf->jdate("Y", $this->start_date), (int)Yii::$app->jdf->jdate("m", $this->start_date));
    }

    /**
     * @return bool
     * کارمندانی که ترک کار شده اند و در دوره ثبت نشده اند.
     */
    public function addEndWorkEmployee()
    {
        $flag = true;
        if (($previousModel = self::find()->byPrevious($this->workshop_id, $this->start_date)->limit(1)->one()) !== null) {
            foreach ($previousModel->getSalaryPeriodItems()
                         ->joinWith(['employee'])
                         ->andWhere(['>=', 'JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")', Yii::$app->jdf->jdate("Y/m/d", $previousModel->start_date)])
                         ->andWhere(['=', 'JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")', Yii::$app->jdf->jdate("Y/m/d", $previousModel->end_date)])
                         ->andWhere(['NOT IN', SalaryPeriodItems::tableName() . '.user_id', $this->getSalaryPeriodItems()->select(['user_id'])])->all() as $employeeLost) {
                /** @var SalaryPeriodItems $employeeLost */
                $model = new SalaryPeriodItems([
                    'period_id' => $this->id,
                    'user_id' => $employeeLost->user_id,
                ]);
                $model->description = 'ترک کار ' . $employeeLost->employee->end_work;
                $flag = $flag && $model->save(false);
            }

        }
        return $flag;
    }


    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Status' => [
                self::STATUS_WAIT_CONFIRM => Yii::t('app', 'Wait Confirm'),
                self::STATUS_CONFIRM => Yii::t('app', 'Confirm And Wait Payment'),
                self::STATUS_PAYMENT => Yii::t('app', 'Payment'),
            ],
            'StatusColor' => [
                self::STATUS_WAIT_CONFIRM => 'warning',
                self::STATUS_CONFIRM => 'info',
                self::STATUS_PAYMENT => 'success',
            ],
            'Kind' => [
                self::KIND_SALARY => 'حقوق',
                self::KIND_REWARD => 'عیدی و پاداش',
                self::KIND_YEAR => 'سنوات',
            ],
            'KindLink' => [
                self::KIND_SALARY => 'salary-period-items/index',
                self::KIND_REWARD => 'reward-period-items/index',
                self::KIND_YEAR => 'year-period-items/index',
            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
        }
        if (in_array($this->getScenario(), [self::SCENARIO_CREATE, self::SCENARIO_CREATE_YEAR])) {
            $this->start_date = strtotime(Jdf::Convert_jalali_to_gregorian($this->start_date) . ' 00:00:00');
            $this->end_date = strtotime(Jdf::Convert_jalali_to_gregorian($this->end_date) . ' 00:00:00');
        }
        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
    }


    public function getRangeDate()
    {
        return Yii::$app->jdf->jdate('Y/m/d', $this->start_date) . " - " . Yii::$app->jdf->jdate('Y/m/d', $this->end_date);
    }
}
