<?php

namespace hesabro\hris\models;

use backend\models\User;
use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\JsonAdditional;
use common\behaviors\LogBehavior;
use common\behaviors\SendAutoCommentsBehavior;
use common\behaviors\TraceBehavior;
use common\components\jdf\Jdf;
use common\interfaces\SendAutoCommentInterface;
use common\models\Account;
use common\models\BalanceDetailed;
use common\models\CommentsType;
use common\models\Customer;
use common\models\Document;
use common\models\mongo\MGLogs;
use common\models\Settings;
use common\models\Year;
use common\validators\DateValidator;
use common\validators\IBANValidator;
use common\validators\NationalCodeValidator;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**serve-report-by-customer-buy
 * This is the model class for table "{{%employee_branch_user}}".
 *
 * @property int $user_id
 * @property int $branch_id
 * @property int $status
 * @property int $deleted_at
 * @property int $salary
 * @property string $shaba
 * @property object $additional_data
 * @property object $pending_data
 *
 * @property EmployeeBranch $branch
 * @property User $user
 * @property SalaryInsurance $salaryInsurance
 * @property Account $account
 * @property int $validAdvanceMoney
 * @property-read bool $isUpdatableByOwner
 * @property-read bool $isConfirmed
 * @property EmployeeChild[] $children
 * @property-read EmployeeChild[] $childrenWithPending
 * @property EmployeeExperience[] $experiences
 * @property EmployeeExperience[] $experiencesWithPending
 *
 * @mixin StorageUploadBehavior
 */
class EmployeeBranchUser extends ActiveRecord implements SendAutoCommentInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    const SCENARIO_UPDATE = "update";
    const SCENARIO_INSURANCE = "insurance";
    const SCENARIO_SET_END_WORK = "set_end_work";
    const SCENARIO_RETURN_END_WORK = "return_end_work";

    const SCENARIO_UPDATE_PROFILE = 'update_profile';

    const SCENARIO_REJECT_UPDATE = 'reject_update';

    const MARITAL_MARRIED = 1;
    const MARITAL_SINGLE = 2;

    const EDUCATION_EBTEDAEI = 1;
    const EDUCATION_MOTEVASETE = 2;
    const EDUCATION_DIPLOM = 3;
    const EDUCATION_KARADANI = 4;
    const EDUCATION_KARSHENASI = 5;
    const EDUCATION_KARSHENASI_ARSHAD = 6;
    const EDUCATION_PHD = 7;

    const SHIFT_ONE = 0;
    const SHIFT_TOW = 1;

    const MAX_FILE_SIZE = 1024 * 1024 * 2;  // 2MB

    const FILE_ATTRIBUTES = [
        'sh_picture_first', 'sh_picture_second', 'sh_picture_third', 'id_card_front', 'id_card_back',
        'education_picture', 'insurance_history', 'resume_file', 'military_doc'
    ];

    public $delete_document_end_work = false;

    public $error_msg = '';

    public $job_code;

    public $insurance_code;

    public $start_work;

    public $end_work;

    public $description_work;

    public $sh_number;

    public $nationalCode;

    public $sex;

    public $birthday;

    public $national;

    public $first_name;

    public $last_name;

    public $father_name;

    public $issue_date;

    public $issue_place;

    public $crm_client_id;

    public $bank_name;

    public $bank_code;

    public $deposit_number;

    public $disable_show_on_salary_list;

    public $marital;

    public $child_count;

    public $education;

    public $work_address;

    public $email;

    public $checkout;

    public $employee_address;

    public $delete_point = 0;

    public $insurance_history_month_count = 0; // تعداد روز سابقه بیمه از شرکت های قبلی

    public $work_history_day_count = 0; // تعداد روز کارکرد ثبت نشده در سیستم از سالهای قبل

    public $shift;

    public $manager; // هیأت مدیره

    public $roll_call_id; // ای دی تفضیل دستگاه حضور و غیاب

    public $shaba_non_cash; // شماره کارت کارانه

    public $account_non_cash; // شماره حساب کارانه

    public $count_insurance_addition = 0; // تعداد نفرات برای کسر بیمه تکمیلی

    public $account_id;

    public $history;

    public $confirmed;

    public $date_of_marriage;

    public $military_description;

    public $children = [];

    public $experiences = [];

    public $avatar = null;

    public $sh_picture_first = null;

    public $sh_picture_second = null;

    public $sh_picture_third = null;

    public $id_card_front = null;

    public $id_card_back = null;

    public $education_picture = null;

    public $insurance_history = null;

    public $resume_file = null;

    public $military_doc = null;

    public $reject_update_description = null;

    public $reject_update_description_seen = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_branch_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $militaryDocIsEmpty = !$this->getFileUrl('military_doc');
        $sexMan = User::SEX_MAN;
        $married = self::MARITAL_MARRIED;
        return [
            [['user_id', 'branch_id'], 'required'],
            [['end_work'], 'required', 'on' => [self::SCENARIO_SET_END_WORK]],
            [['salary', 'branch_id', 'account_id'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [['start_work', 'birthday', 'issue_date'], DateValidator::class, 'on' => [self::SCENARIO_INSURANCE, self::SCENARIO_UPDATE_PROFILE]],
            [['nationalCode'], NationalCodeValidator::class, 'on' => [self::SCENARIO_INSURANCE, self::SCENARIO_UPDATE_PROFILE]],
            [['checkout'], 'boolean', 'on' => [self::SCENARIO_INSURANCE]],
            [['end_work'], DateValidator::class, 'on' => [self::SCENARIO_SET_END_WORK]],
            [['end_work'], 'validateEndWork', 'skipOnError' => false, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_SET_END_WORK]],
            [['user_id', 'branch_id', 'salary', 'marital', 'child_count', 'education', 'shift', 'roll_call_id', 'count_insurance_addition'], 'integer'],
            [['user_id', 'deleted_at'], 'unique', 'message' => 'این کارمند قبلا در شعبه دیگری تعریف شده است.'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeBranch::class, 'targetAttribute' => ['branch_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['shaba'], IBANValidator::class, 'on' => [self::SCENARIO_UPDATE]],
            [['shaba_non_cash', 'account_non_cash'], 'string', 'on' => [self::SCENARIO_UPDATE]],
            [['manager'], 'boolean'],
            [['confirmed'], 'boolean', 'on' => [self::SCENARIO_UPDATE]],
            [['delete_point'], 'integer', 'on' => [self::SCENARIO_UPDATE]],
            [['delete_document_end_work'], 'boolean', 'on' => [self::SCENARIO_RETURN_END_WORK]],

            // update profile
            ['email', 'email', 'on' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE]],
            [
                [
                    'sex', 'first_name', 'last_name'
                ],
                'required',
                'on' => [self::SCENARIO_INSURANCE]
            ],
            [
                [
                    'first_name', 'last_name', 'father_name', 'birthday', 'nationalCode', 'sh_number', 'issue_date',
                    'issue_place', 'national', 'sex', 'marital', 'education', 'insurance_history_month_count',
                    'employee_address', 'email'
                ],
                'required',
                'on' => [self::SCENARIO_UPDATE_PROFILE]
            ],
            [
                [
                    'first_name', 'last_name', 'father_name', 'birthday', 'nationalCode', 'sh_number', 'issue_date',
                    'issue_place', 'national', 'sex', 'marital', 'education', 'insurance_history_month_count',
                    'employee_address', 'sh_picture_first', 'sh_picture_second', 'sh_picture_third', 'id_card_front',
                    'id_card_back', 'education_picture', 'insurance_history', 'resume_file', 'military_doc', 'military_description'
                ],
                'validateCanUpdateByOwner',
                'on' => [self::SCENARIO_UPDATE_PROFILE]
            ],
            [
                [
                    'sh_picture_first', 'sh_picture_second', 'sh_picture_third',
                    'id_card_front', 'id_card_back', 'military_doc',
                    'education_picture', 'insurance_history',
                ],
                'file',
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'],
                'maxSize' => self::MAX_FILE_SIZE,
                'on' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE]
            ],
            [
                [
                    'resume_file'
                ],
                'file',
                'extensions' => ['pdf'],
                'mimeTypes' => ['application/pdf'],
                'maxSize' => self::MAX_FILE_SIZE,
                'on' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE]
            ],
            [
                'date_of_marriage',
                'required',
                'when' => fn(self $model) => $model->marital == self::MARITAL_MARRIED,
                'whenClient' => "function(attribute, value) {
                    const marriedStatus = $('#marital-select')

                    return marriedStatus.val() === '$married'
                }",
                'on' => [self::SCENARIO_UPDATE_PROFILE]
            ],
            [
                'date_of_marriage',
                'validateDateOfMarriage',
                'on' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE]
            ],
            [
                ['children', 'experiences'],
                'safe',
                'on' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE]
            ],
            [
                'military_doc',
                'required',
                'when' => fn(self $model) => $model->sex == $sexMan && !$model->military_description && $militaryDocIsEmpty,
                'whenClient' => "function(attribute, value) {
                    const sex = $('#sex-select')
                    const militaryDes = $('#employeebranchuser-military_description')
                    return sex.val() === '$sexMan' && !militaryDes.val() && Boolean($militaryDocIsEmpty)
                }",
                'message'=>'درصورت نداشتن مدرک نظام وظیفه، در خط بعد 👇🏻 تیک مدرک ندارم را انتخاب فرمائید.',
                'on' => [self::SCENARIO_UPDATE_PROFILE]
            ],
            [
                'military_description',
                'required',
                'when' => fn(self $model) => $model->sex == $sexMan && (!$model->military_doc && $militaryDocIsEmpty),
                'whenClient' => "function(attribute, value) {
                    const sex = $('#sex-select')
                    const militaryDoc = $('#employeebranchuser-military_doc')
                    return sex.val() === '$sexMan' && !militaryDoc.val() && Boolean($militaryDocIsEmpty)
                }",
                'message' => Yii::t('app', 'Required When Field Empty', [
                    'attribute' => $this->getAttributeLabel('military_description'),
                    'field' => $this->getAttributeLabel('military_doc')
                ]),
                'on' => [self::SCENARIO_UPDATE_PROFILE]
            ],
            ['reject_update_description', 'required', 'on' => [self::SCENARIO_REJECT_UPDATE]],
            ['reject_update_description', 'string', 'on' => [self::SCENARIO_REJECT_UPDATE]],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_UPDATE] = ['salary', 'shaba', 'shaba_non_cash', 'account_non_cash', 'delete_point', 'branch_id', 'branch_id', 'shift', 'roll_call_id', 'manager', 'email', 'account_id', 'confirmed'];
        $scenarios[self::SCENARIO_SET_END_WORK] = ['end_work'];
        $scenarios[self::SCENARIO_RETURN_END_WORK] = ['delete_document_end_work'];
        $scenarios[self::SCENARIO_REJECT_UPDATE] = ['reject_update_description'];
        $scenarios[self::SCENARIO_INSURANCE] = [
            'job_code', 'insurance_code', 'start_work', 'end_work', 'checkout', 'description_work', 'sh_number',
            'nationalCode', 'sex', 'birthday', 'national', 'first_name', 'last_name', 'father_name', 'issue_date',
            'issue_place', 'marital', 'child_count', 'education', 'work_address', 'employee_address',
            'insurance_history_month_count', 'work_history_day_count', 'email', 'sh_picture_first', 'sh_picture_second',
            'sh_picture_third', 'id_card_front', 'id_card_back', 'education_picture', 'insurance_history',
            'date_of_marriage', 'resume_file', 'military_doc', 'military_description', 'count_insurance_addition'
        ];
        $scenarios[self::SCENARIO_UPDATE_PROFILE] = [
            'first_name', 'last_name', 'father_name', 'birthday', 'nationalCode', 'sh_number', 'issue_date',
            'issue_place', 'national', 'sex', 'marital', 'education', 'insurance_history_month_count',
            'work_history_day_count', 'email', 'employee_address', 'sh_picture_first', 'sh_picture_second',
            'sh_picture_third', 'id_card_front', 'id_card_back', 'education_picture', 'insurance_history',
            'date_of_marriage', 'resume_file', 'military_doc', 'military_description', 'child_count'
        ];

        return $scenarios;
    }

    public function validateDateOfMarriage($attribute, $params)
    {
        if (is_null(Jdf::jalaliToTimestamp((Jdf::tr_num($this->date_of_marriage)), 'Y/m/d'))) {
            $this->addError($attribute, Yii::t('app', 'Date Of Marriage Is Invalid'));
        }
    }


    public function validateEndWork($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->end_work < $this->start_work || $this->end_work > Yii::$app->jdf->jdate("Y/m/d")) {
                $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
            } elseif (($year = Year::find()->byDate($this->end_work)->one()) === null || !$year->isSetSettingForYearPeriod()) {
                $this->addError($attribute, 'برای سال مورد نظر تنظیمات اولیه حقوق و دستمزد ست نشده است');
            } elseif (($lastPayment = SalaryPeriodItems::find()->byUser($this->user_id)->joinWith(['period'])->limit(1)->andWhere(['>', 'basic_salary', 0])->orderBy(['start_date' => SORT_DESC])->one()) !== null && $this->end_work != Yii::$app->jdf->jdate("Y/m/d", $lastPayment->period->end_date)) {
                $this->addError($attribute, "تاریخ ترک کار باید برابر با " . Yii::$app->jdf->jdate("Y/m/d", $lastPayment->period->end_date) . " باشد.");
            }
        }
    }

    public function validateCanUpdateByOwner($attribute, $params)
    {
        if (in_array($attribute, self::FILE_ATTRIBUTES) && !$this->getFileUrl($attribute)) {
            return;
        }

        if ($this->isConfirmed) {
            $this->addError($attribute, 'امکان ویرایش وجود ندارد.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'branch_id' => Yii::t('app', 'Branch ID'),
            'salary' => Yii::t('app', 'Salary'),
            'shaba' => Yii::t('app', 'Shaba Number'),
            'delete_document_end_work' => 'حذف سند ترک کار',

            'job_code' => 'کد شغل',
            'insurance_code' => 'شماره بیمه',
            'start_work' => 'تاریخ شروع به کار',
            'end_work' => 'تاریخ ترک کار',
            'checkout' => 'تسویه کامل',
            'description_work' => 'شرح شغل',
            'sh_number' => 'شماره شناسنامه',
            'nationalCode' => 'کد ملی',
            'sex' => 'جنسیت',
            'birthday' => 'تاریخ تولد',
            'national' => 'ملیت',
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'father_name' => 'نام پدر',
            'issue_date' => 'تاریخ صدور',
            'issue_place' => 'محل صدور',
            'delete_point' => 'کسر امتیازات از حقوق پایه',
            'marital' => 'وضعیت تاهل',
            'child_count' => 'تعداد فرزند',
            'education' => 'مدرک تحصیلی',
            'employee_address' => 'آدرس کارمند',
            'employee_number' => 'شماره',
            'company_name' => 'نام شرکت',
            'company_ceo' => 'نام مدیر شرکت',
            'company_number' => 'شماره شرکت',
            'company_address' => 'آدرس شرکت',
            'work_address' => 'آدرس محل کار',
            'insurance_history_month_count' => 'سابقه بیمه',
            'work_history_day_count' => 'سابقه کارکرد',
            'shift' => 'شیفت کاری',
            'manager' => 'هیأت مدیره',
            'confirmed' => 'تایید حساب کاربری',
            'roll_call_id' => 'ای دی تفضیل دستگاه حضور و غیاب',
            'status' => Yii::t('app', 'State'),
            'email' => Yii::t('app', 'Email'),
            'account_id' => Yii::t('app', 'Account ID'),
            'sh_picture_first' => Yii::t('app', 'Birth Certificate First Page'),
            'sh_picture_second' => Yii::t('app', 'Birth Certificate Second Page'),
            'sh_picture_third' => Yii::t('app', 'Birth Certificate Third Page'),
            'id_card_front' => Yii::t('app', 'ID Card Front'),
            'id_card_back' => Yii::t('app', 'ID Card Back'),
            'resume_file' => Yii::t('app', 'Resume'),
            'military_doc' => Yii::t('app', 'Evidence') . ' ' . Yii::t('app', 'Military Service'),
            'military_description' => Yii::t('app', 'Description') . ' ' . Yii::t('app', 'Military Service'),
            'education_picture' => Yii::t('app', 'Last Degree Of Education'),
            'insurance_history' => Yii::t('app', 'Insurance History'),
            'shaba_non_cash' =>'شماره کارت کارانه',
            'account_non_cash' =>'شماره حساب کارانه',
            'date_of_marriage' => Yii::t('app', 'Date Of Marriage'),
            'reject_update_description' => Yii::t('app', 'Reject Description'),
            'count_insurance_addition' => 'تعداد بیمه تکمیلی',
        ];
    }

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'confirmed' => 'در صورت فعال بودن این گزینه، کارمند امکان ویرایش اطلاعات کاربری خود را نخواهد داشت',
            'military_doc' => 'کارت پایان خدمت، کارت معافیت از خدمت، معافیت تحصیلی، ...',
            'email' => $this->user?->email ? "پیام‌های اطلاع‌رسانی حسابرو به {$this->user->email} ارسال می‌شوند." : '',
            'insurance_history_month_count' => 'تعداد روز سابقه بیمه از شرکت های قبلی',
            'work_history_day_count' => 'تعداد روز کارکرد ثبت نشده در سیستم از سالهای قبل'
        ]);
    }

    public function getIsUpdatableByOwner()
    {
        return !$this->start_work;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(EmployeeBranch::class, ['id' => 'branch_id']);
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
    public function getSalaryInsurance()
    {
        return $this->hasOne(SalaryInsurance::class, ['id' => 'job_code']);
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
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * {@inheritdoc}
     * @return EmployeeBranchUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new EmployeeBranchUserQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    public function canCreateSalaryPayment()
    {
        if (!empty($this->end_work)) {
            $this->error_msg = 'این کارمند ترک کار شده است.';
            return false;
        }
        if ($this->disable_show_on_salary_list) {
            $this->error_msg = 'این کارمند غیر فعال شده است.';
            return false;
        }

        if (!$this->account_id) {
            $this->error_msg = 'حساب تفضیل کارمند ست نشده است.لطفا از قسمت بروزرسانی ثبت نمایید';
            return false;
        }
        if (empty($this->job_code)) {
            $this->error_msg = 'کد شغلی کارمند ست نشده است.';
            return false;
        }

        return true;
    }

    public function canCreateRewardPayment()
    {

        if ($this->status == self::STATUS_DELETED) {
            return false;
        }
        return !empty($this->job_code);
    }

    public function canCreateYearPayment()
    {
        if ($this->status == self::STATUS_DELETED) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     *  امکان واریز حقوق
     */
    public function canPaymentSalary(): bool
    {
        if (empty($this->shaba)) {
            $this->error_msg = "شماره شبا کارمند {$this->user->fullName} ثبت نشده است.";
            return false;
        }
        return true;
    }

    public function canSetEndWork()
    {
        if (!empty($this->end_work)) {
            $this->error_msg = 'تاریخ ترک کار ثبت شده است.';
            return false;
        }
        return true;
    }

    public function canReturnEndWork()
    {
        if (empty($this->end_work)) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * شروع کار مجدد
     */
    public function canStartWorkAgain()
    {
        if (empty($this->end_work)) {
            return false;
        }
        return true;
    }

    public function getValidAdvanceMoney()
    {
        return $this->salary / 3;
    }

    /**
     * @return array
     * get list of insurance data
     */
    public function getInsuranceData($for_contract = false)
    {
        $data = [
            'first_name' => $this->first_name ?: $this->user->first_name,
            'last_name' => $this->last_name ?: $this->user->last_name,
            'father_name' => $this->father_name,
            'sh_number' => $this->sh_number,
            'nationalCode' => $this->nationalCode ?: ($this->user->customer ? $this->user->customer->nationalID : null),
            'national' => is_array(Customer::itemAlias('National', $this->national)) ? null : Customer::itemAlias('National', $this->national),
            'sex' => is_array(Customer::itemAlias('SexTitle', $this->sex)) ? null : Customer::itemAlias('SexTitle', $this->sex),
            'birthday' => $this->birthday ?: ($this->user->customer ? $this->user->customer->birthday : null),
            'marital' => is_array(self::itemAlias('marital', $this->marital)) ? null : self::itemAlias('marital', $this->marital),
            'child_count' => $this->child_count,
            'education' => is_array(self::itemAlias('education', $this->education)) ? null : self::itemAlias('education', $this->education),
            'employee_address' => $this->employee_address ?: ($this->user->customer && $this->user->customer->address ? $this->user->customer->address[0]->adress : null),
            'employee_number' => $this->user->username,
            'shaba' => 'IR' . $this->shaba,
            'shaba_non_cash' => $this->shaba_non_cash,
            'account_non_cash' => $this->account_non_cash,
            'issue_date' => $this->issue_date,
            'issue_place' => $this->issue_place,
            'job_code' => $this->job_code,
            'insurance_code' => $this->insurance_code,
            'start_work' => $this->start_work,
            'end_work' => $this->end_work,
            'description_work' => $this->description_work,
            'work_address' => $this->work_address,
        ];

        if ($for_contract) {
            $data += [
                'company_name' => Settings::get('business_name', true),
                'company_ceo' => Settings::get('employee_company_ceo', true),
                'company_number' => Settings::get('business_phone_number', true),
                'company_address' => Settings::get('business_address', true),
                'company_national_code' => Settings::get('national_id', true),
            ];
        }

        return $data;
    }


    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteWithLog()
    {
        $modelMgLog = new MGLogs();
        $modelMgLog->client_id = \Yii::$app->client->id;
        $modelMgLog->model_class = EmployeeBranch::class;
        $modelMgLog->model_id = (int)$this->branch_id;
        $modelMgLog->action = 'delete';
        $modelMgLog->logs = ['delete' => 'کارمند ' . $this->user_id . ' - ' . $this->user->fullName];
        $flag = $modelMgLog->save();

        $modelMgLogUser = new MGLogs();
        $modelMgLogUser->client_id = \Yii::$app->client->id;
        $modelMgLogUser->model_class = EmployeeBranchUser::class;
        $modelMgLogUser->model_id = (int)$this->user_id;
        $modelMgLogUser->action = 'delete';
        $modelMgLogUser->logs = ['delete' => 'شعبه‌ی ' . $this->branch_id . ' - ' . $this->branch->title];
        $flag = $flag && $modelMgLogUser->save();

        return $flag && $this->softDelete();
    }

    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        $this->deleted_at = time();
        return $this->save();
    }


    public function saveDocumentEndWork()
    {
        $document = new Document();
        $document->type = Document::TYPE_CHECKOUT_EMPLOYEE;
        $document->is_auto = 1;
        $document->model_id = $this->user_id;
        $document->h_date = $this->end_work;
        $document->des = 'تسویه حساب کارمند ' . $this->user->fullName;
        $flag = $document->save();

        $year = Year::find()->byDate($this->end_work)->one();
        /****************** محاسبه سنوات ******************/
        $modelSalaryPeriodItemsYear = new SalaryPeriodItems(['user_id' => $this->user_id, 'yearModel' => $year]);
        $modelSalaryPeriodItemsYear->loadDefaultValuesBeforeCreateYear(strtotime(Jdf::Convert_jalali_to_gregorian($this->end_work) . ' 23:59:59'), $year);
        $debtor = BalanceDetailed::getBalance(Settings::get('year_period_m_id', true), $this->account_id, true);

        if (($yearPayment = $modelSalaryPeriodItemsYear->payment_salary - $debtor) > 0) {
            /****************** بدهکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('year_period_interface', true), null, $yearPayment, 0, $document->des . ' - سنوات ' . $modelSalaryPeriodItemsYear->hours_of_work . ' روز');
            /****************** بستانکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('year_period_m_id', true), $this->account_id, 0, $yearPayment, $document->des . ' - سنوات ' . $modelSalaryPeriodItemsYear->hours_of_work . ' روز');
        }

        /****************** محاسبه عیدی پاداش ******************/
        $modelSalaryPeriodItemsReward = new SalaryPeriodItems(['user_id' => $this->user_id, 'yearModel' => $year]);
        $modelSalaryPeriodItemsReward->loadDefaultValuesBeforeCreateReward(strtotime(Jdf::Convert_jalali_to_gregorian($this->end_work) . ' 23:59:59'), $year);


        $modelSalaryPeriodItemsReward->tax = $modelSalaryPeriodItemsReward->calculateTaxReward($modelSalaryPeriodItemsReward->getTotalInYear() + $modelSalaryPeriodItemsReward->total_salary - ($year->COST_TAX_STEP_1_MIN) - ((int)$modelSalaryPeriodItemsReward->getTotalInYear('insurance') * 2 / 7));
        $modelSalaryPeriodItemsReward->payment_salary = (int)($modelSalaryPeriodItemsReward->total_salary - $modelSalaryPeriodItemsReward->tax);
        /****************** بدهکار ******************/
        if ($modelSalaryPeriodItemsReward->total_salary > 0) {
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('reward_period_m_id', true), null, $modelSalaryPeriodItemsReward->total_salary, 0, $document->des . ' - عیدی پاداش ');
        }

        /****************** بستانکار ******************/
        if ($modelSalaryPeriodItemsReward->tax > 0) {
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('salary_period_tax_m_id', true), Settings::get('salary_period_tax_t_id', true), 0, $modelSalaryPeriodItemsReward->tax, $document->des . ' - عیدی پاداش ');
        }

        if ($modelSalaryPeriodItemsReward->payment_salary > 0) {
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('reward_period_payment_m_id', true), $this->account_id, 0, $modelSalaryPeriodItemsReward->payment_salary, $document->des . ' - عیدی پاداش ');
        }

        /****************** کسر مساعده ******************/
        if ($modelSalaryPeriodItemsReward->advance_money > 0) {
            /****************** بدهکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('salary_period_payment_m_id', true), $this->account_id, $modelSalaryPeriodItemsReward->advance_money, 0, $document->des . ' - کسر مساعده');
            /****************** بستانکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Settings::get('m_debtor_advance_money', true), $this->account_id, 0, $modelSalaryPeriodItemsReward->advance_money, $document->des . ' - کسر مساعده');

        }
        return $flag && $document->validateTaraz();
    }


    /**
     * @return Document|null
     */
    public function getDocumentEndWork()
    {
        return Document::find()->byModel($this->user_id, Document::TYPE_CHECKOUT_EMPLOYEE)->limit(1)->orderBy(['h_date' => SORT_DESC])->one();
    }

    /**
     * @return bool
     */
    public function deleteDocumentEndWork()
    {
        $document = Document::find()->findByModel($this->user_id)->findByType(Document::TYPE_CHECKOUT_EMPLOYEE)->one();
        if ($document !== null) {
            if (!$document->canDelete(false, false)) {
                //$this->error_msq = 'امکان حذف سند حسابداری وجود ندارد.';
                return false;
            }
            if (!$document->delete()) {
                //$this->error_msq = 'امکان حذف سند حسابداری وجود ندارد.';
                return false;
            }
        }
        return true;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return bool|int|string|null
     */
    public function getMonthWork($startDate, $endDate)
    {
        if ($this->start_work > $startDate) {
            $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($this->start_work) . ' 00:00:00');
        } else {
            $startTime = strtotime(Jdf::Convert_jalali_to_gregorian($startDate) . ' 00:00:00');
        }
        $endTime = strtotime(Jdf::Convert_jalali_to_gregorian($endDate) . ' 23:59:59');
        return SalaryPeriodItems::find()
            ->andWhere(['user_id' => $this->user_id])
            ->bySalary()
            ->byYear($startTime, $endTime)
            ->count();
    }

    /**
     * @return bool
     */
    public function saveLowTime($date, $low_time, $period_id): bool
    {
        if ($low_time > 0) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_LOW_TIME,
                'type' => SalaryItemsAddition::TYPE_LOW_DELAY,
                'from_date' => $date,
                'second' => $low_time,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'period_id' => $period_id
            ]);
            return $model->save();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function saveOverTime($date, $over_time, $period_id): bool
    {
        if ($over_time > 0) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_OVER_TIME,
                'type' => SalaryItemsAddition::TYPE_OVER_TIME_DAY,
                'from_date' => $date,
                'second' => $over_time,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => $over_time < 400 ? SalaryItemsAddition::STATUS_CONFIRM : SalaryItemsAddition::STATUS_WAIT_CONFIRM,
                'period_id' => $period_id
            ]);
            return $model->save();
        }
        return true;
    }

    public function getIsConfirmed(): bool
    {
        return is_null($this->confirmed) || $this->confirmed;
    }

    public static function itemAlias($type, $code = null)
    {
        $_items = [
            'Status' => [
                self::STATUS_ACTIVE => Yii::t('app', 'Status Active'),
                self::STATUS_DELETED => Yii::t('app', 'Deleted'),
            ],
            'marital' => [
                self::MARITAL_MARRIED => 'متاهل',
                self::MARITAL_SINGLE => 'مجرد',
            ],
            'Shift' => [
                self::SHIFT_ONE => 'یک شیفت',
                self::SHIFT_TOW => 'دو شیفت',
            ],
            'education' => [
                self::EDUCATION_EBTEDAEI => 'ابتدایی',
                self::EDUCATION_MOTEVASETE => 'متوسطه',
                self::EDUCATION_DIPLOM => 'دیپلم',
                self::EDUCATION_KARADANI => 'فوق دیپلم',
                self::EDUCATION_KARSHENASI => 'لیسانس',
                self::EDUCATION_KARSHENASI_ARSHAD => 'فوق لیسانس',
                self::EDUCATION_PHD => 'دکترا',
            ],
            'insuranceDataDefaultVariables' => [
                'first_name' => 'نام',
                'last_name' => 'نام خانوادگی',
                'father_name' => 'نام پدر',
                'sh_number' => 'شماره شناسنامه',
                'nationalCode' => 'کد ملی',
                'national' => 'ملیت',
                'sex' => 'جنسیت',
                'birthday' => 'تاریخ تولد',
                'marital' => 'وضعیت تاهل',
                'child_count' => 'تعداد فرزند',
                'education' => 'مدرک تحصیلی',
                'employee_address' => 'آدرس',
                'employee_number' => 'شماره تماس',
                'shaba' => 'شماره شبا',
                'issue_date' => 'تاریخ صدور شناسنامه',
                'issue_place' => 'محل صدور شناسنامه',
                'job_code' => 'کد شغل',
                'insurance_code' => 'کد بیمه',
                'description_work' => 'توضیحات شغل',
                'work_address' => 'آدرس محل کار',
                'company_name' => 'نام شرکت',
                'company_ceo' => 'نام مدیرعامل شرکت',
                'company_number' => 'شماره شرکت',
                'company_address' => 'آدرس شرکت',
                'company_national_code' => 'شناسه ملی شرکت',
                'daily_salary' => 'حقوق پایه روزانه',
                'right_to_housing' => 'حق مسکن',
                'right_to_food' => 'حق خواربار',
                'rightToChild' => 'حق اولاد',
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
                'ownerClassName' => self::class,
                'saveAfterInsert' => true,
                'ownerPrimaryKey' => 'user_id',
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::EMPLOYEE_UPDATE_PROFILE,
                'title' => 'درخواست ویرایش حساب کاربری',
                'scenarioValid' => [self::SCENARIO_UPDATE_PROFILE],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::EMPLOYEE_UPDATE_PROFILE_REJECT,
                'title' => 'رد درخواست ویرایش حساب کاربری',
                'scenarioValid' => [self::SCENARIO_REJECT_UPDATE],
                'callAfterUpdate' => true
            ],
            'StorageUploadBehavior' => [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_EMPLOYEE_BRANCH_USER,
                'attributes' => [
                    'sh_picture_first', 'sh_picture_second', 'sh_picture_third',
                    'id_card_front', 'id_card_back', 'resume_file', 'military_doc',
                    'education_picture', 'insurance_history'
                ],
                'scenarios' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'primaryKey' => 'user_id',
            ],
            'JsonAdditional' => [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'notSaveNull' => true,
                'AdditionalDataProperty' => [
                    'job_code' => 'String',
                    'insurance_code' => 'String',
                    'start_work' => 'String',
                    'end_work' => 'String',
                    'description_work' => 'String',
                    'sh_number' => 'String',
                    'nationalCode' => 'String',
                    'sex' => 'String',
                    'birthday' => 'String',
                    'national' => 'String',
                    'first_name' => 'String',
                    'last_name' => 'String',
                    'father_name' => 'String',
                    'issue_date' => 'String',
                    'issue_place' => 'String',
                    'delete_point' => 'Integer',
                    'marital' => 'Integer',
                    'child_count' => 'Integer',
                    'education' => 'Integer',
                    'employee_address' => 'String',
                    'work_address' => 'String',
                    'insurance_history_month_count' => 'Integer',
                    'work_history_day_count' => 'Integer',
                    'shift' => 'Integer',
                    'manager' => 'Boolean',
                    'confirmed' => 'Boolean',
                    'checkout' => 'Boolean',
                    'roll_call_id' => 'Integer',
                    'email' => 'String',
                    'account_id' => 'Integer',
                    'date_of_marriage' => 'String',
                    'military_description' => 'String',
                    'reject_update_description' => 'String',
                    'reject_update_description_seen' => 'Boolean',
                    'history' => 'ClassArray::' . EmployeeHistory::class,
                    'children' => 'ClassArray::' . EmployeeChild::class,
                    'experiences' => 'ClassArray::' . EmployeeExperience::class,
                    'shaba_non_cash' => 'String',
                    'account_non_cash' => 'String',
                    'count_insurance_addition' => 'Integer',
                ],

            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (in_array($this->scenario, [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE])) {
            $this->date_of_marriage = $this->marital == self::MARITAL_MARRIED ? $this->date_of_marriage : null;
            $this->children = $this->marital == self::MARITAL_MARRIED ? $this->children : [];
            if ($this->military_doc) {
                $this->military_description = null;
            }

            $this->children = array_map(function (EmployeeChild $child) {
                $child->beforeSave();
                return $child->attributes;
            }, array_filter($this->children ?: [], fn(EmployeeChild $child) => !$child->deleted));

            $this->experiences = array_map(function (EmployeeExperience $xp) {
                $xp->beforeSave();
                return $xp->attributes;
            }, array_filter($this->experiences ?: [], fn(EmployeeExperience $xp) => !$xp->deleted));
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (
            $this->scenario === self::SCENARIO_INSURANCE &&
            $this->job_code &&
            $salaryInsurance = SalaryInsurance::findOne($this->job_code)
        ) {
            if ($salaryInsurance->tag_id && $customer = Customer::find()->findByUser($this->user_id)->one()) {
                $customer->jobs = [$salaryInsurance->tag_id];
                $customer->save(false);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function saveToPending(): bool
    {
        $attributes = [
            'first_name', 'last_name', 'father_name', 'birthday', 'nationalCode', 'sh_number', 'issue_date',
            'national', 'sex', 'education', 'email', 'insurance_history_month_count', 'marital', 'date_of_marriage',
            'employee_address', 'issue_place', 'child_count', 'work_history_day_count', 'military_description'
        ];
        $pendingData = [];

        $newModel = clone $this;
        $this->refresh();
        $this->children = [];
        $this->experiences = [];
        $this->getBehavior('JsonAdditional')->afterFind();
        $this->getBehavior('StorageUploadBehavior')->beforeValidate();
        $this->reject_update_description = null;
        $this->reject_update_description_seen = false;

        if ($this->isChildrenChanged($newModel->children ?: [])) {
            $pendingData['children'] = array_map(fn(EmployeeChild $child) => get_object_vars($child),$newModel->children ?: []);
        }

        if ($this->isExperiencesChanged($newModel->experiences ?: [])) {
            $pendingData['experiences'] = array_map(fn(EmployeeExperience $xp) => get_object_vars($xp),$newModel->experiences ?: []);
        }

        foreach (get_object_vars($newModel) as $key => $value) {
            if (in_array($key, $attributes) && $this->$key != $value) {
                $pendingData[$key] = $value;
            }
        }

        $this->pending_data = count($pendingData) ? $pendingData : null;

        return $this->save(false);
    }

    public function getAttributeValue($attribute, bool $pendingValue = false)
    {
        if ($pendingValue && $this->hasPendingData($attribute)) {
            return ArrayHelper::getValue($this->pending_data, $attribute);
        }

        return ArrayHelper::getValue(array_merge($this->attributes, $this->additional_data ?: []), $attribute);
    }

    public function hasPendingData(string $attribute): bool
    {
        if (is_array($this->pending_data) && count($this->pending_data)) {
            $old = ArrayHelper::getValue(array_merge($this->attributes, $this->additional_data ?: []), $attribute);
            $new = ArrayHelper::getValue($this->pending_data, $attribute);

            return !is_null($new) && $old != $new;
        }
        return false;
    }

    public function getPendingDataHint(string $attribute, bool $mainValue = false, string $default = null): array
    {
        if ($this->hasPendingData($attribute)) {
            $value = ArrayHelper::getValue($mainValue ? array_merge($this->attributes, $this->additional_data ?: []) : $this->pending_data, $attribute);
            $attr = explode('.', $attribute);
            $value = match (end($attr)) {
                'marital' => self::itemAlias('marital', $value),
                'education' => self::itemAlias('education', $value),
                'sex' => User::itemAlias('Sex', $value),
                'national' => Customer::itemAlias('National', $value),
                'insurance' => EmployeeChild::itemAlias('insurance', $value),
                default => $value
            };

            if (!is_array($value)) {
                return [
                    $mainValue ?
                        Yii::t('app', 'Old Value',) . ": $value" :
                        Yii::t('app', 'Pending Value') . ": $value",
                    ['class' => 'profile-input-hint']
                ];
            }
        }

        return [
            !is_null($default) ? $default : $this->getAttributeHint($attribute),
            ['class' => 'text-muted']
        ];
    }

    public function getChildrenWithPending(): array
    {
        /**
         * @type EmployeeChild[] $children
         * @type EmployeeChild[]|null $childrenPending
         */

        $children = $this->children ?: [];
        $childrenPending = $this->pending_data['children'] ?? null;

        if (is_null($childrenPending)) {
            return $children;
        }

        $pendingUuid = array_map(fn(array $child) => $child['uuid'], $childrenPending);
        $existsUuid = array_map(fn(EmployeeChild $child) => $child->uuid, $children);

        $childrenWithPending = [];
        foreach ($children as $child) {
            $child->deleted = !in_array($child->uuid, $pendingUuid);
            $child->added = false;
            $childrenWithPending[] = $child;
        }

        foreach ($childrenPending as $child) {
            if (in_array($child['uuid'], $existsUuid)) {
                continue;
            }
            $child['deleted'] = false;
            $child['added'] = true;
            $childrenWithPending[] = new EmployeeChild($child);
        }

        return $childrenWithPending;
    }

    public function getExperiencesWithPending(): array
    {
        /**
         * @type EmployeeExperience[] $experiences
         * @type EmployeeExperience[]|null $experiencesPending
         */

        $experiences = $this->experiences ?: [];
        $experiencesPending = $this->pending_data['experiences'] ?? null;

        if (is_null($experiencesPending)) {
            return $experiences;
        }

        $existsUuid = array_map(fn(EmployeeExperience $xp) => $xp->uuid, $experiences);
        $pendingUuid = array_map(fn(array $xp) => $xp['uuid'], $experiencesPending);

        $experiencesWithPending = [];
        foreach ($experiences as $xp) {
            $xp->deleted = !in_array($xp->uuid, $pendingUuid);
            $xp->added = false;
            $experiencesWithPending[] = $xp;
        }

        foreach ($experiencesPending as $xp) {
            if (in_array($xp['uuid'], $existsUuid)) {
                continue;
            }
            $xp['deleted'] = false;
            $xp['added'] = true;
            $experiencesWithPending[] = new EmployeeExperience($xp);
        }

        return $experiencesWithPending;
    }

    public function seenRejectUpdate(): bool
    {
        if ($self = self::findOne(['user_id' => $this->user_id, 'branch_id' => $this->branch_id])) {
            $self->reject_update_description_seen = true;

            return $self->save(false);
        }

        return false;
    }

    public function getContentMail(): string
    {
        if ($this->scenario === self::SCENARIO_UPDATE_PROFILE) {
            return "یک درخواست ویرایش حساب کاربری برای {$this->user->fullName} ثبت شد.";
        }

        if ($this->scenario === self::SCENARIO_REJECT_UPDATE) {
            return "درخواست ویرایش حساب کاربری برای {$this->user->fullName} رد شد.";
        }

        return '';
    }

    public function getLinkMail(): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/comfort/items']);
    }

    public function getUserMail(): array
    {
        return [$this->user_id];
    }

    public function autoCommentCondition(): bool
    {
        if ($this->scenario === self::SCENARIO_UPDATE_PROFILE && !$this->isConfirmed) {
            return false;
        }

        return true;
    }

    /**
     * @param EmployeeChild[] $children
     * @return bool
     */
    public function isChildrenChanged(array $children): bool
    {
        $thisChildren = $this->children ?: [];

        if (count($thisChildren) !== count($children)) {
            return true;
        }

        foreach ($children as $child) {
            $correspond = current(array_filter($thisChildren, fn(EmployeeChild $item) => $item->uuid === $child->uuid));

            if (!$correspond) {
                return true;
            }

            if (count($child->diff($correspond))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param EmployeeExperience[] $experiences
     * @return bool
     */
    public function isExperiencesChanged(array $experiences): bool
    {
        $thisExperiences = $this->experiences ?: [];

        if (count($thisExperiences) !== count($experiences)) {
            return true;
        }

        foreach ($experiences as $xp) {
            $correspond = current(array_filter($thisExperiences, fn(EmployeeExperience $item) => $item->uuid === $xp->uuid));

            if (!$correspond) {
                return true;
            }

            if (count($xp->diff($correspond))) {
                return true;
            }
        }

        return false;
    }
}
