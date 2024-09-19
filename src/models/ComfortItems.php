<?php

namespace hesabro\hris\models;

use backend\models\AdvanceMoney;
use backend\models\User;
use common\behaviors\CdnUploadFileBehavior;
use common\behaviors\JsonAdditional;
use common\behaviors\LogBehavior;
use common\behaviors\SendAutoCommentsBehavior;
use common\behaviors\TraceBehavior;
use common\components\Jdate;
use common\components\jdf\Jdf;
use common\interfaces\SendAutoCommentInterface;
use common\models\Comments;
use common\models\CommentsType;
use common\models\Customer;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%employee_comfort_items}}".
 *
 * @property int $id
 * @property int|null $comfort_id
 * @property int|null $user_id
 * @property float $amount
 * @property string|null $attach
 * @property string|null $description
 * @property string|null $additional_data
 * @property int $status
 * @property int $created
 * @property int $creator_id
 * @property int $update_id
 * @property int $changed
 * @property EmployeeBranchUser $employee
 *
 * @property User $creator
 * @property User $update
 * @property User $user
 * @property SalaryPeriodItems $salaryItems
 * @property Comfort $comfort
 * @property-read Comments[] $comments
 */
class ComfortItems extends \yii\db\ActiveRecord implements SendAutoCommentInterface
{
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_REJECT = 3;
    const STATUS_DELETED = 0;

    const SCENARIO_CREATE = 'create';

    const SCENARIO_LOAN_CREATE = 'loan_create';

    const SCENARIO_UPDATE_ADMIN = 'update_admin';

    const SCENARIO_CONFIRM = 'confirm';

    const SCENARIO_REJECT = 'reject';

    const SCENARIO_DELETE = 'delete';

    const SCENARIO_REVERT = 'revert';

    const LOAN_INSTALLMENTS = [
        1, 2, 3
    ];

    public ?EmployeeBranchUser $employee = null;

    public $file_name;

    public string $error_msg = '';
    public bool $saveAdvanceMoney = false;

    /** additional data */
    public ?string $reject_description = null;

    public mixed $loan_installment = null;

    public mixed $advance_money = null;

    public mixed $comments_count = 0;

    public mixed $salary_items_addition_id = null;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_comfort_items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comfort_id', 'user_id', 'amount', 'created', 'creator_id', 'update_id', 'changed', 'status'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string'],
            [['saveAdvanceMoney'], 'boolean', 'on' => [self::SCENARIO_UPDATE_ADMIN]],
            [['additional_data'], 'safe'],
            [['loan_installment'], 'in', 'range' => self::LOAN_INSTALLMENTS],
            [['comfort_id', 'user_id', 'amount'], 'required'],
            [['amount'], 'validateComfort', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_LOAN_CREATE]],
            [['amount'], 'validateComfortAdminConfirm', 'on' => [self::SCENARIO_UPDATE_ADMIN]],
            [['file_name'], 'file', 'skipOnEmpty' => false, 'extensions' => ['jpg', 'jpeg', 'png', 'pdf'], 'maxSize' => 1024 * 1024 * 8],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['comfort_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comfort::class, 'targetAttribute' => ['comfort_id' => 'id'],
                'filter' => function ($query) {
                    /** @var ComfortQuery $query */
                    return $query->canShow($this->employee);
                },
                'on' => [self::SCENARIO_CREATE]
            ]
        ];
    }

    /**
     * @return array|array[]
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $dynamicFields = [];


        if ($this->comfort->document_required) {
            $dynamicFields[] = 'file_name';
        }

        $scenarios[self::SCENARIO_DELETE] = [];
        $scenarios[self::SCENARIO_CREATE] = array_merge(['!comfort_id', '!user_id', 'amount', 'description'], $dynamicFields);
        $scenarios[self::SCENARIO_LOAN_CREATE] = array_merge(['!comfort_id', '!user_id', 'amount', 'loan_installment', 'description'], $dynamicFields);
        $scenarios[self::SCENARIO_UPDATE_ADMIN] = ['amount', 'description', 'saveAdvanceMoney'];
        $scenarios[self::SCENARIO_CONFIRM] = ['amount', 'description', 'saveAdvanceMoney', 'salary_items_addition_id'];
        $scenarios[self::SCENARIO_REJECT] = ['reject_description'];
        $scenarios[self::SCENARIO_REVERT] = ['status', 'description', 'reject_description'];

        return $scenarios;
    }


    public function validateComfort($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->comfort->showcase) {
                $this->addError($attribute, "امکان رفاهی {$this->comfort->title} قابل درخواست نمی‌باشد.");
            }

            if (
                (is_array($this->comfort->users) && count($this->comfort->users) && !in_array($this->user_id, $this->comfort->users)) ||
                (is_array($this->comfort->excluded_users) && count($this->comfort->excluded_users) && in_array($this->user_id, $this->comfort->excluded_users))
            ) {
                $this->addError($attribute, 'شما مجاز به استفاده از این امکان رفاهی نیستید.');
            }

            $excludedJobs = $this->comfort->excluded_jobs;
            $jobs = $this->comfort->jobs;
            $jobTags = array_map(fn($item) => $item, Customer::find()->findByUser(Yii::$app->user->identity->id)->one()->jobs ?: []);
            if (
                (is_array($excludedJobs) && count($excludedJobs) && (array_intersect($excludedJobs, $jobTags))) ||
                (is_array($jobs) && count($jobs) && (!array_intersect($jobs, $jobTags)))
            ) {
                $this->addError($attribute, 'شما مجاز به استفاده از این امکان رفاهی نیستید.');
            }

            if ($this->comfort->type_limit == Comfort::TYPE_LIMIT_YEARLY) {
                if ($this->comfort->amount_limit > 0 && $this->comfort->amount_limit < ((int)self::find()->byComfort($this->comfort_id)->byUser($this->user_id)->andWhere(['<>', 'id', (int)$this->id])->thisYear()->notReject()->sum('amount') + $this->amount)) {
                    $this->addError($attribute, 'مبلغ مورد نظر از حد مجاز سالانه بیشتر می باشد.');
                } elseif ($this->comfort->count_limit > 0 && $this->comfort->count_limit < ((int)self::find()->byComfort($this->comfort_id)->byUser($this->user_id)->andWhere(['<>', 'id', (int)$this->id])->thisYear()->notReject()->count() + 1)) {
                    $this->addError($attribute, 'تعداد از حد مجاز سالانه بیشتر می باشد.');
                }
            } elseif ($this->comfort->type_limit == Comfort::TYPE_LIMIT_MONTHLY) {
                if ($this->comfort->amount_limit > 0 && $this->comfort->amount_limit < ((int)self::find()->byComfort($this->comfort_id)->byUser($this->user_id)->andWhere(['<>', 'id', (int)$this->id])->thisMonth()->notReject()->sum('amount') + $this->amount)) {
                    $this->addError($attribute, 'مبلغ مورد نظر از حد مجاز ماهانه بیشتر می باشد.');
                } elseif ($this->comfort->count_limit > 0 && $this->comfort->count_limit < ((int)self::find()->byComfort($this->comfort_id)->byUser($this->user_id)->andWhere(['<>', 'id', (int)$this->id])->thisMonth()->notReject()->count() + 1)) {
                    $this->addError($attribute, 'تعداد امکانات از حد مجاز ماهانه بیشتر می باشد.');
                }
            }

            $experience = (int) $this->comfort->experience_limit;
            if ($experience > 0) {
                $workTime = (new SalaryPeriodItemsSearch())->totalWorkByUser($this->user_id);
                if ($workTime < ($experience * 30)) {
                    $this->addError($attribute, "برای ایجاد این درخواست حداقل سابقه کار باید $experience ماه باشد.");
                }
            }

            $requestAgainLimit = (int) $this->comfort->request_again_limit;
            if ($requestAgainLimit > 0) {
                $lastComfortItem = (new ComfortItemsSearch())->lastComfortItemByUser($this->comfort_id, $this->user_id);
                $nextTimestamp = strtotime("+$requestAgainLimit days", $lastComfortItem?->created);

                if ($lastComfortItem && time() < $nextTimestamp) {
                    $nextDate = Yii::$app->jdate->date('Y/m/d', $nextTimestamp);
                    $this->addError($attribute, "شما تا تاریخ $nextDate امکان ثبت این درخواست ندارید.");
                }
            }

            $monthLimit = $this->comfort->month_limit ?: [];
            $monthLimitStr = implode('، ', array_map(fn($m) => Jdate::getMonthNames($m), $monthLimit));
            if (count($monthLimit)) {
                $month = (int) Jdate::date('m');
                if (!in_array($month, $monthLimit)) {
                    $monthWord = count($monthLimit) > 1 ? 'ماه‌های' : 'ماه‌';
                    $this->addError($attribute, "امکان ثبت درخواست فقط در $monthWord $monthLimitStr وجود دارد.");
                }
            }

            $dayLimitStart = $this->comfort->day_limit_start;
            $dayLimitEnd = $this->comfort->day_limit_end;
            if ($dayLimitStart > 0 || $dayLimitEnd > 0) {
                $day = (int) Jdate::date('d');
                $dayLimitStartStr = $dayLimitStart ? $dayLimitStart . (!$monthLimitStr ? ' ام' : '') : null;
                $dayLimitEndStr = $dayLimitEnd ? $dayLimitEnd . (!$monthLimitStr ? ' ام' : '') : null;
                $monthWord = $monthLimitStr ?: 'هر ماه';
                $rangeMessage = $dayLimitStart > 0 && $dayLimitEnd > 0 ? "امکان ثبت درخواست فقط از $dayLimitStartStr تا $dayLimitEndStr $monthWord وجود دارد." : null;

                if ($dayLimitStart > 0 && $dayLimitStart > $day) {
                    $this->addError($attribute, $rangeMessage ?: "امکان ثبت درخواست فقط از $dayLimitStartStr $monthWord به بعد وجود دارد.");
                }

                if ($dayLimitEnd > 0 && $dayLimitEnd < $day) {
                    $this->addError($attribute, $rangeMessage ?: "امکان ثبت درخواست فقط تا $dayLimitEndStr $monthWord وجود دارد.");
                }
            }
        }
    }


    public function validateComfortAdminConfirm($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->comfort->type_limit == Comfort::TYPE_LIMIT_YEARLY) {
                if ($this->comfort->amount_limit > 0 && $this->comfort->amount_limit < ((int)self::find()->byComfort($this->comfort_id)->byUser($this->user_id)->andWhere(['<>', 'id', (int)$this->id])->thisYear($this->created)->notReject()->sum('amount') + $this->amount)) {
                    $this->addError($attribute, 'مبلغ مورد نظر از حد مجاز سالانه بیشتر می باشد.');
                }
            } elseif ($this->comfort->type_limit == Comfort::TYPE_LIMIT_MONTHLY) {
                if ($this->comfort->amount_limit > 0 && $this->comfort->amount_limit < ((int)self::find()->byComfort($this->comfort_id)->byUser($this->user_id)->andWhere(['<>', 'id', (int)$this->id])->thisMonth($this->created)->notReject()->sum('amount') + $this->amount)) {
                    $this->addError($attribute, 'مبلغ مورد نظر از حد مجاز ماهانه بیشتر می باشد.');
                }
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
            'comfort_id' => Yii::t('app', 'Type'),
            'user_id' => Yii::t('app', 'User ID'),
            'amount' => Yii::t('app', 'Amount'),
            'attach' => Yii::t('app', 'Attach'),
            'file_name' => Yii::t('app', 'Request Documents'),
            'description' => Yii::t('app', 'Description'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'status' => Yii::t('app', 'State'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'changed' => Yii::t('app', 'Changed'),
            'reject_description' => Yii::t('app', 'Reject Description'),
            'saveAdvanceMoney' => 'ثبت مساعده',
            'loan_installment' => 'تعداد اقساط'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreator(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdate(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'update_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalaryItems()
    {
        return $this->hasMany(SalaryPeriodItems::class, ['user_id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComfort(): ActiveQuery
    {
        return $this->hasOne(Comfort::class, ['id' => 'comfort_id']);
    }

    public function getComments(): ActiveQuery
    {
        return $this->hasMany(Comments::class, ['class_id' => 'id'])->andWhere(['class_name' => self::class]);
    }

    /**
     * {@inheritdoc}
     * @return ComfortItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ComfortItemsQuery(get_called_class());
        return $query->active();
    }

    public function setEmployee()
    {
        if ($this->employee === null) {
            $this->employee = EmployeeBranchUser::find()->andWhere(['user_id' => $this->user_id])->limit(1)->one();
        }
    }

    public function canUpdate(): bool
    {
        return true;
    }

    public function canDelete(): bool
    {
        return $this->status == self::STATUS_WAIT_CONFIRM;
    }

    public function canConfirm(): bool
    {
        if ($this->status != self::STATUS_WAIT_CONFIRM) {
            $this->error_msg = Yii::t('app', 'It is not possible to perform this operation');
            return false;
        }
        $this->setEmployee();
        if ($this->employee === null) {
            $this->error_msg = 'متاسفانه اطلاعات پرسنلی شما ثبت نشده است.';
            return false;
        }
        if (!$this->employee->shaba) {
            $this->error_msg = 'متاسفانه اطلاعات شبا کارمند برای ثبت درخواست حواله ثبت نشده است.';
            return false;
        }

        return true;
    }

    public function canReject(): bool
    {
        return $this->status == self::STATUS_WAIT_CONFIRM;
    }

    public function canRevert(): bool
    {
        if ($this->status === self::STATUS_WAIT_CONFIRM) {
            $this->addError('id', 'امکان بازگشت به حالت قبل وجود ندارد.');
            return false;
        }

        if ($this->salary_items_addition_id) {
            $salaryItemsAddition = SalaryItemsAddition::find()->where(['id' => $this->salary_items_addition_id])->one();

            if ($salaryItemsAddition && !$salaryItemsAddition->canDelete()) {
                $this->addError('id', 'امکان بازگشت به حالت قبل وجود ندارد.');
                return false;
            }
        }

        $advanceMoney = $this->advance_money ? AdvanceMoney::find()
            ->andWhere(['id' => $this->advance_money])
            ->andWhere(['status' => AdvanceMoney::STATUS_CONFIRM])
            ->one() : null;

        if ($advanceMoney && !$advanceMoney->canDelete()) {
            $this->addError('id', 'این درخواست رفاهی دارای مساعده تایید شده می‌باشد.');
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function softDelete(): bool
    {
        $this->status = self::STATUS_DELETED;
        $this->scenario = self::SCENARIO_DELETE;
        return $this->save();
    }


    /**
     * @return bool
     */
    public function confirm(): bool
    {
        $this->status = self::STATUS_CONFIRM;
        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function saveAdvanceMoney(): bool
    {
        $this->setEmployee();

        $model = new AdvanceMoney([
            'scenario' => AdvanceMoney::SCENARIO_CREATE_AUTO,
            'user_id' => $this->user_id,
            'amount' => ((int)($this->amount * 70 / 100)),
            'model_class' => self::class,
            'model_id' => $this->id,
            'comment' => "تایید درخواست رفاهیه " . $this->comfort->title,
            'iban' => $this->employee->shaba,
        ]);

        if ($model->save()) {
            $this->advance_money = $model->id;
            return $this->save();
        }

        return false;
    }

    public function deleteAdvanceMoney(): bool
    {
        if(!$this->advance_money) {
            return true;
        }

        $advanceMoney  = AdvanceMoney::findOne($this->advance_money);

        $transaction = Yii::$app->db->beginTransaction();


        $flag = $advanceMoney->canDelete() && $advanceMoney->softDelete();
        $this->advance_money = null;

        if ($flag && $this->save(false)) {
            $transaction->commit();
            return true;
        }

        $transaction->rollBack();
        return false;
    }

    /**
     * @return bool
     */
    public function returnStatus(): bool
    {
        $this->status = self::STATUS_WAIT_CONFIRM;
        return $this->save(false);
    }

    /**
     * @param $description
     * @return bool
     */
    public function reject($description): bool
    {
        $this->status = self::STATUS_REJECT;
        $this->reject_description = $description;
        return $this->save(false);
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Status' => [
                self::STATUS_WAIT_CONFIRM => Yii::t('app', 'Wait Confirm'),
                self::STATUS_CONFIRM => Yii::t('app', 'Confirm'),
                self::STATUS_REJECT => Yii::t('app', 'Reject'),
            ],
            'StatusClass' => [
                self::STATUS_WAIT_CONFIRM => 'warning',
                self::STATUS_CONFIRM => 'success',
                self::STATUS_REJECT => 'danger',
            ],
            'StatusIcon' => [
                self::STATUS_WAIT_CONFIRM => 'ph:clock-countdown-duotone',
                self::STATUS_CONFIRM => 'ph:check-circle-duotone',
                self::STATUS_REJECT => 'ph:x-circle-duotone'
            ],
            'Installments' => array_combine(ComfortItems::LOAN_INSTALLMENTS, array_map(fn($value) => "هر $value ماه یک چک", ComfortItems::LOAN_INSTALLMENTS))
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
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ],
            [
                'class' => CdnUploadFileBehavior::class,
                'model_class' => 'Comfort',
                'allowed_mime_types' => 'application,image',
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'reject_description' => 'String',
                    'loan_installment' => 'Integer',
                    'advance_money' => 'NullInteger',
                    'salary_items_addition_id' => 'NullInteger'
                ]
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_COMFORT,
                'title' => 'ثبت درخواست امکانات رفاهی',
                'scenarioValid' => [self::SCENARIO_CREATE, self::SCENARIO_LOAN_CREATE]
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_COMFORT_CONFIRM,
                'title' => 'تایید درخواست امکانات رفاهی',
                'scenarioValid' => [self::SCENARIO_CONFIRM],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_COMFORT_REJECT,
                'title' => 'رد درخواست امکانات رفاهی',
                'scenarioValid' => [self::SCENARIO_REJECT],
                'callAfterUpdate' => true
            ]
        ];
    }

    public function setScenarioByComfort(string $action, Comfort $comfort = null): void
    {
        $comfort = $comfort ?: $this->comfort;

        $scenario = match ($action) {
            self::SCENARIO_CREATE => [
                Comfort::CAT_TYPE_COMFORT => self::SCENARIO_CREATE,
                Comfort::CAT_TYPE_MEDICAL => self::SCENARIO_CREATE,
                Comfort::CAT_TYPE_LOAN => self::SCENARIO_LOAN_CREATE,
                Comfort::CAT_TYPE_OTHER => self::SCENARIO_CREATE,
            ],
            self::SCENARIO_UPDATE_ADMIN => self::SCENARIO_UPDATE_ADMIN,
            default => self::SCENARIO_DEFAULT
        };

        $this->scenario = is_array($scenario) ? ($scenario[$comfort->type] ?? self::SCENARIO_DEFAULT) : $scenario;
    }

    public function getContentMail(): string
    {
        if (in_array($this->scenario, [self::SCENARIO_CREATE, self::SCENARIO_LOAN_CREATE])) {
            $type = Comfort::itemAlias('TypeCat', $this->comfort->type);
            return "یک درخواست $type برای {$this->user->fullName} ثبت شد.";
        }

        if ($this->scenario === self::SCENARIO_CONFIRM) {
            $type = Comfort::itemAlias('TypeCat', $this->comfort->type);
            return "درخواست $type شما تایید شد.";
        }

        if ($this->scenario === self::SCENARIO_REJECT) {
            $type = Comfort::itemAlias('TypeCat', $this->comfort->type);
            return "درخواست $type شما رد شد.";
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
        return true;
    }

    public function createComment(Comments $comment): bool
    {
        if (!is_array($comment->owner) || !count($comment->owner)) {

            return true;
        }

        $parent = Comments::find()->isParent()->where([
            'class_name' => self::class,
            'class_id' => $this->id
        ])->limit(1)->one();

        $formName = (new ComfortItemsSearch())->formName();
        $comment->is_duty = true;
        $comment->parent_id = $parent?->id ?: 0;
        $comment->type = Comments::TYPE_PRIVATE;
        $comment->status = Comments::STATUS_ACTIVE;
        $comment->class_name = self::class;
        $comment->class_id = $this->id;
        $comment->creator_id = Yii::$app->user->identity->getId();
        $comment->link = Yii::$app->urlManager->createAbsoluteUrl(['/employee/comfort-items/index', $formName . '[id]' => $this->id]);
        $comment->title = implode(' ', [
            Yii::t('app', 'Refer'),
            Yii::t('app', 'Comfort Items'),
            $this->comfort->title,
            $this->user->fullName
        ]);

        return $comment->save() && $comment->saveInbox();
    }

    public function createSalaryItemAddition(): bool
    {
        $type = (int) $this->comfort->salary_items_addition;

        if ($type && $type !== Comfort::SALARY_ITEM_IGNORE) {

            $kind = match ($type) {
                SalaryItemsAddition::TYPE_COMMISSION_REWARD,
                SalaryItemsAddition::TYPE_COMMISSION_SPECIAL_DAY,
                SalaryItemsAddition::TYPE_COMMISSION_BIRTHDAY => SalaryItemsAddition::KIND_COMMISSION,
                SalaryItemsAddition::TYPE_PAY_BUY,
                SalaryItemsAddition::TYPE_NON_CASH_CREDIT_CARD => SalaryItemsAddition::KIND_NON_CASH,
            };

            $salaryItemsAddition = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'user_id' => $this->user_id,
                'kind' => $kind,
                'type' => $type,
                'second' => $this->amount,
                'from_date' => time(),
                'to_date' => 0,
                'description' => $this->description
            ]);

            if (!$salaryItemsAddition->save()) {
                return false;
            }

            $this->salary_items_addition_id = $salaryItemsAddition->id;
            return $this->save(false);
        }

        return true;
    }

    public function deleteSalaryItemAddition(): bool
    {
        if ($this->salary_items_addition_id) {
            return SalaryItemsAddition::findOne($this->salary_items_addition_id)?->softDelete();
        }

        return true;
    }
}
