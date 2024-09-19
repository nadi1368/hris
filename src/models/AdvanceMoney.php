<?php

namespace hesabro\hris\models;

use common\behaviors\SendAutoCommentsBehavior;
use backend\modules\employee\models\EmployeeBranchUser;
use common\behaviors\JsonAdditional;
use common\behaviors\LogBehavior;
use common\behaviors\TraceBehavior;
use common\components\jdf\Jdf;
use common\interfaces\SendAutoCommentInterface;
use common\models\AccountDefinite;
use common\models\CommentsType;
use common\validators\IBANValidator;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%advance_money}}".
 *
 * @property int $id
 * @property string $comment
 * @property string $reject_comment
 * @property int $user_id
 * @property int $amount
 * @property string $receipt_number
 * @property string $receipt_date
 * @property int $status
 * @property int $doc_id
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 *
 * @property User $user
 * @property User $update
 * @property EmployeeBranchUser $employee
 */
class AdvanceMoney extends \yii\db\ActiveRecord implements SendAutoCommentInterface
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_REJECT = 3;

    const SCENARIO_CREATE = "create";
    const SCENARIO_CREATE_AUTO = "create_auto";
    const SCENARIO_REJECT = "reject";
    const SCENARIO_CONFIRM = "confirm";
    const SCENARIO_CREATE_INFINITE = "create-infinite";
    const SCENARIO_CREATE_WITH_CONFIRM = "create-with-confirm";

    const validRequestCount = 3;

    public $error_msg = '';

    /** additional data */
    public $iban;
    public $model_class;
    public $model_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%advance_money}}';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'iban'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_CREATE_AUTO]],
            [['iban'], IBANValidator::class],
            [['reject_comment'], 'required', 'on' => self::SCENARIO_REJECT],
            [['comment', 'reject_comment'], 'string'],
            [['amount'], 'number', 'min' => 1000],
            [['amount'], 'validateAmountAndCount', 'on' => self::SCENARIO_CREATE],
            [['user_id', 'status', 'doc_id', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['comment', 'amount', 'iban'];
        $scenarios[self::SCENARIO_CREATE_AUTO] = ['comment', 'amount', 'iban'];
        $scenarios[self::SCENARIO_REJECT] = ['reject_comment'];
        $scenarios[self::SCENARIO_CONFIRM] = ['!status'];
        $scenarios[self::SCENARIO_CREATE_INFINITE] = ['user_id', 'amount', 'comment'];
        $scenarios[self::SCENARIO_CREATE_WITH_CONFIRM] = ['user_id', 'amount', 'comment'];

        return $scenarios;
    }

    public function validateAmountAndCount($attribute, $params, $validator, $current)
    {
        [$start, $end] = Jdf::getStartAndEndOfCurrentMonth();

        $totalAmountRequest = (int)self::find()
            ->my($this->user_id)
            ->andWhere(['IN', 'status', self::itemAlias('StatusValid')])
            ->andWhere(['between', 'created', $start, $end])
            ->sum('amount');
        $employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $this->user_id])->one();
        if (!$this->hasErrors()) {
            if (($totalAmountRequest + $this->amount) > $employeeUser->validAdvanceMoney) {
                $this->addError($attribute, 'سقف مبلغ مجاز درخواست شما در این ماه به اتمام رسیده است.');
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
            'comment' => Yii::t('app', 'Description'),
            'amount' => Yii::t('app', 'Amount'),
            'user_id' => Yii::t('app', 'User ID'),
            'status' => Yii::t('app', 'Status'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'created' => Yii::t('app', 'Created'),
            'changed' => Yii::t('app', 'Changed'),
            'iban' => Yii::t('app', 'Shaba Number'),
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getMDebtor()
    {
        return $this->hasOne(AccountDefinite::class, ['id' => 'm_debtor_id']);
    }

    /**
     * {@inheritdoc}
     * @return AdvanceMoneyQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AdvanceMoneyQuery(get_called_class());
        return $query->active();
    }

    public function canCreate()
    {
        $employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $this->user_id])->one();
        if ($employeeUser === null) {
            $this->error_msg = 'متاسفانه اطلاعات پرسنلی شما ثبت نشده است.';
            return false;
        }

        [$start, $end] = Jdf::getStartAndEndOfCurrentMonth();

        $countRequest = self::find()
            ->my($this->user_id)
            ->andWhere(['IN', 'status', self::itemAlias('StatusValid')])
            ->andWhere(['between', 'created', $start, $end])
            ->count();
        if ($countRequest > self::validRequestCount) {
            $this->error_msg = 'سقف تعداد مجاز درخواست شما در این ماه به اتمام رسیده است.';
            return false;
        }

        $totalAmountRequest = self::find()
            ->my($this->user_id)
            ->andWhere(['IN', 'status', self::itemAlias('StatusValid')])
            ->andWhere(['between', 'created', $start, $end])
            ->sum('amount');
        if (($totalAmountRequest + $this->amount) > $employeeUser->validAdvanceMoney) {
            $this->error_msg = 'سقف مبلغ مجاز درخواست شما در این ماه به اتمام رسیده است.';
            return false;
        }
        return true;
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM;
    }

    public function canReject()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM;
    }

    public function canConfirm()
    {
        if ($this->status != self::STATUS_WAIT_CONFIRM) {
            return false;
        }
        $employeeUser = EmployeeBranchUser::find()->andWhere(['user_id' => $this->user_id])->one();
        if ($employeeUser === null) {
            $this->error_msg = 'متاسفانه اطلاعات پرسنلی شما ثبت نشده است.';
            return false;
        }

        [$start, $end] = Jdf::getStartAndEndOfCurrentMonth();

//        $countRequest = self::find()
//            ->my($this->user_id)
//            ->andWhere(['IN', 'status', self::itemAlias('StatusValid')])
//            ->andWhere(['between', 'created', $start, $end])
//            ->count();
//        if ($countRequest-1 > self::validRequestCount) {
//            $this->error_msg = 'سقف تعداد مجاز درخواست شما در این ماه به اتمام رسیده است.';
//            return false;
//        }

//        $totalAmountRequest = self::find()
//            ->my($this->user_id)
//            ->andWhere(['IN', 'status', self::itemAlias('StatusValid')])
//            ->andWhere(['between', 'created', $start, $end])
//            ->sum('amount');
//        if (($totalAmountRequest ) > $employeeUser->validAdvanceMoney) {
//            $this->error_msg = 'سقف مبلغ مجاز درخواست شما در این ماه به اتمام رسیده است.';
//            return false;
//        }
        return true;
    }


    /*
    * حذف منطقی
    */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save();
    }

    /**
     * @return array
     */
    public function getUserMail(): array
    {
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM, self::SCENARIO_REJECT])) {
            return [$this->user_id];
        }
        return [];
    }

    /**
     * @return string
     */
    public function getLinkMail(): string
    {
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM, self::SCENARIO_REJECT])) {
            return '';
        }
        return Yii::$app->urlManager->createAbsoluteUrl(['/employee/advance-money-manage/index', 'id' => $this->id]);
    }

    /**
     * @return string
     */
    public function getContentMail(): string
    {
        $content = '';
        if ($this->getScenario() == self::SCENARIO_CREATE) {
            $content = Html::tag('p', "یک درخواست مساعده در سیستم ثبت شده است.");
            $content .= Html::tag('p', 'این درخواست توسط "' . $this->user->fullName . '" در سیستم ثبت شد.');
            $content .= Html::tag('p', 'مبلغ درخواست : "' . number_format((float)$this->amount) . '"');
        }
        if ($this->getScenario() == self::SCENARIO_REJECT) {
            $content = Html::tag('p', "متاسفانه درخواست مساعده شما مورد تایید قرار نگرفت.");
            $content .= Html::tag('p', 'این درخواست توسط "' . $this->update->fullName . '" رد شد.');
            $content .= Html::tag('p', 'توضیحات رد درخواست : "' . $this->reject_comment . '"');
            $content .= Html::tag('p', 'مبلغ درخواست : "' . number_format((float)$this->amount) . '"');
        }
        if ($this->getScenario() == self::SCENARIO_CONFIRM) {
            $content = Html::tag('p', "درخواست مساعده شما مورد تایید قرار گرفت.");
            $content .= Html::tag('p', 'این درخواست توسط "' . $this->update->fullName . '" تایید شد.');
            $content .= Html::tag('p', 'مبلغ درخواست : "' . number_format((float)$this->amount) . '"');
        }
        return $content;
    }

    public function autoCommentCondition(): bool
    {
        return true;
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Status' => [
                self::STATUS_WAIT_CONFIRM => Yii::t('app', 'Wait Confirm'),
                self::STATUS_CONFIRM => Yii::t('app', 'Status Confirm'),
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
            'StatusValid' => [
                self::STATUS_WAIT_CONFIRM,
                self::STATUS_CONFIRM,
            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }


    public function beforeSave($insert)
    {
        if ($this->getScenario() != self::SCENARIO_CREATE_WITH_CONFIRM) {
            if ($this->isNewRecord) {
                $this->created = time();
                $this->creator_id = Yii::$app->user->id;
                $this->status = self::STATUS_WAIT_CONFIRM;
                $this->comment = Html::encode($this->comment);
            }
            $this->update_id = Yii::$app->user->id;
            $this->changed = time();
        }
        return parent::beforeSave($insert);
    }


    public function behaviors()
    {
        return [
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ],
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_DRAFT_REPORT,
                'title' => 'ثبت درخواست مساعده',
                'scenarioValid' => [self::SCENARIO_CREATE, self::SCENARIO_CREATE_AUTO]
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_DRAFT_REPORT,
                'title' => 'رد درخواست مساعده',
                'scenarioValid' => [self::SCENARIO_REJECT],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_DRAFT_REPORT,
                'title' => 'تایید درخواست مساعده',
                'scenarioValid' => [self::SCENARIO_CONFIRM],
                'callAfterUpdate' => true
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'iban' => 'String',
                    'model_class' => 'String',
                    'model_id' => 'Integer',
                ],

            ],
        ];
    }
}
