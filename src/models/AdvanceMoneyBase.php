<?php

namespace hesabro\hris\models;

use hesabro\helpers\behaviors\WageBehavior;
use hesabro\hris\Module;
use Yii;
use yii\helpers\Html;
use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\validators\IBANValidator;

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
 * @property object $user
 * @property object $update
 * @property EmployeeBranchUser $employee
 */
class AdvanceMoneyBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_CONFIRM = 2;
    const STATUS_REJECT = 3;
    const STATUS_PAYED_BY_BOOM = 4;
    const STATUS_MULTI_PAY_LIST = 5;

    const SCENARIO_CREATE = "create";
    const SCENARIO_CREATE_AUTO = "create_auto";
    const SCENARIO_CONFIRM = "confirm";
    const SCENARIO_REJECT = "reject";
    const SCENARIO_CREATE_INFINITE = "create-infinite";
    const SCENARIO_BACKEND_FINNO = 'backend_finno';
    const SCENARIO_BACKEND_BOOM = 'backend_boom';
    const SCENARIO_CREATE_WITH_CONFIRM = "create-with-confirm";

    const validRequestCount = 3;

    public $t_creditor_id, $m_debtor_id;
    public $error_msg = '';

    public $btn_type;
    public $document;

    /** additional data */
    public $iban;
    public $status_transfer_to_multi_pay;
    public $model_class;
    public $model_id;
    public $documentsArray;

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
            [['receipt_number', 'receipt_date', 't_creditor_id', 'm_debtor_id'], 'required', 'on' => [self::SCENARIO_CONFIRM, self::SCENARIO_BACKEND_FINNO, self::SCENARIO_BACKEND_BOOM]],
            [['comment', 'reject_comment'], 'string'],
            [['amount'], 'number', 'min' => 1000],
            [['btn_type'], 'string'],
            ['status_transfer_to_multi_pay', 'boolean'],
            [['amount'], 'validateAmountAndCount', 'on' => self::SCENARIO_CREATE],
            [['user_id', 'status', 'doc_id', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['comment', 'amount', 'iban'];
        $scenarios[self::SCENARIO_CREATE_AUTO] = ['comment', 'amount', 'iban'];
        $scenarios[self::SCENARIO_REJECT] = ['reject_comment'];
        $scenarios[self::SCENARIO_CONFIRM] = ['receipt_number', 'receipt_date', 't_creditor_id', 'm_debtor_id', 'btn_type'];
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
            'id' => Module::t('module', 'ID'),
            'comment' => Module::t('module', 'Description'),
            'amount' => Module::t('module', 'Amount'),
            'user_id' => Module::t('module', 'User ID'),
            'status' => Module::t('module', 'Status'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'created' => Module::t('module', 'Created'),
            'changed' => Module::t('module', 'Changed'),
            'receipt_number' => Module::t('module', 'Receipt Number'),
            'receipt_date' => Module::t('module', 'Receipt Date'),
            't_creditor_id' => Module::t('module', 'T Id Creditor'),
            'm_debtor_id' => Module::t('module', 'M Id Debtor'),
            'wage_amount' => Module::t('module', 'Wage'),
            'wage_type' => Module::t('module', 'Type') . ' ' . Yii::t('app', 'Wage'),
            'iban' => Module::t('module', 'Shaba Number'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(EmployeeBranchUser::class, ['user_id' => 'user_id']);
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
        if (!$employeeUser->shaba) {
            $this->error_msg = 'متاسفانه اطلاعات شبا شما ثبت نشده است.';
            return false;
        }
        if (!$employeeUser->account_id) {
            $this->error_msg = 'متاسفانه اطلاعات حساب تفضیل شما ثبت نشده است.';
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

        [$start, $end] = Yii::$app->jdf::getStartAndEndOfCurrentMonth();

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


    /**
     * @return bool
     */
    public function sendToMultiPayList(): bool
    {
        $this->status_transfer_to_multi_pay = true;
        $this->status = self::STATUS_MULTI_PAY_LIST;
        return $this->save(false);
    }

    /**
     * @return bool
     */
    public function removeFromMultiPayList(): bool
    {
        $this->status_transfer_to_multi_pay = false;
        $this->status = self::STATUS_WAIT_CONFIRM;
        return $this->save(false);
    }

    /*
    * حذف منطقی
    */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save();
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Status' => [
                self::STATUS_WAIT_CONFIRM => Module::t('module', 'Wait Confirm'),
                self::STATUS_CONFIRM => Module::t('module', 'Status Confirm'),
                self::STATUS_REJECT => Module::t('module', 'Reject'),
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
            'wageBehavior' => WageBehavior::class,
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
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'iban' => 'String',
                    'model_class' => 'String',
                    'model_id' => 'Integer',
                    'status_transfer_to_multi_pay' => "Boolean",
                    'documentsArray' => "Any"
                ],
            ],
        ];
    }
}
