<?php

namespace hesabro\hris\models;

use backend\models\AdvanceMoney;
use common\behaviors\TraceBehavior;
use common\behaviors\WageBehavior;
use common\components\jdf\Jdf;
use common\models\Account;
use common\models\AccountDefinite;
use common\models\Document;
use common\models\Settings;
use common\validators\DateValidator;
use common\validators\DefiniteValidator;
use Yii;
use yii\base\Model;

/**
 * Class AdvanceMoneyForm
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 *
 * @property AccountDefinite $definiteFrom
 * @property Account $accountFrom
 * @property Account $accountTo
 *
 * @mixin WageBehavior
 */
class AdvanceMoneyForm extends Model
{
    public ?int $definite_id_from = null;
    public  $account_id_from = null;
    public ?int $user_id = null;
    public ?int $account_id_to = null; // حساب تفضیل حقوق کاربر

    public ?int $amount = null;
    public ?string $description = null;
    public ?string $date = null;
    public $btn_type;
    /** @var EmployeeBranchUser $employee */
    public $employee;

    public $document_id;
    public $error_msg = '';


    public function rules()
    {
        return [
            [['definite_id_from', 'account_id_to', 'amount', 'date', 'description'], 'required'],
            [['account_id_from', 'definite_id_from', 'account_id_to', 'amount'], 'integer'],
            [['date'], DateValidator::class],
            [['btn_type', 'description'], 'string'],
            [['account_id_from'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id_from' => 'id']],
            [['account_id_to'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id_to' => 'id']],
            [['definite_id_from'], 'exist', 'skipOnError' => true, 'targetClass' => AccountDefinite::class, 'targetAttribute' => ['definite_id_from' => 'id']],
            [['definite_id_from'], DefiniteValidator::class,
                'skipOnError' => false,
                'skipOnEmpty' => false,
                'definiteId' => 'definite_id_from',
                'accountId' => 'account_id_from',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'definite_id_from' => Yii::t('app', 'M Id Creditor'),
            'account_id_from' => Yii::t('app', 'T Id Creditor'),
            'account_id_to' => 'تفضیل کارمند (بدهکار)',
            'amount' => Yii::t('app', 'Amount'),
            'date' => Yii::t('app', 'Date'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return AccountDefinite|null
     */
    public function getDefiniteFrom(): ?AccountDefinite
    {
        return AccountDefinite::findOne($this->definite_id_from);
    }

    /**
     * @return Account|null
     */
    public function getAccountFrom(): ?Account
    {
        return Account::findOne($this->account_id_from);
    }

    /**
     * @return Account|null
     */
    public function getAccountTo(): ?Account
    {
        return Account::findOne($this->account_id_to);
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        if ($this->account_id_to === null || $this->accountTo === null) {
            $this->error_msg = 'حساب تفضیل کارمند ست نشده است';
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws \yii\base\ExitException
     */
    public function saveDocument(): bool
    {
        $document = new Document();
        $document->type = Document::TYPE_EMPLOYEE_ADVANCE_MONEY;
        $document->model_id = $this->user_id;
        $document->is_auto = Document::AUTO_YES;
        $document->h_date = $this->date;
        $document->des = $this->description;
        $flag = $document->save();
        /****************** بدهکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Settings::get('m_debtor_advance_money', true), $this->account_id_to, $this->amount, 0, $document->des, $document->h_date); // مساعده حقوق به تفضیل کارمند
        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWithWage($this->definite_id_from, $this->account_id_from, 0, $this->amount, $this->wage_type, $this->wage_amount, $document->des, $document->h_date);

        $this->document_id = $flag ? $document->id : 0;
        return $flag && $document->validateTaraz();
    }


    /**
     * @return bool
     */
    public function saveAdvanceMoney(): bool
    {
        $model = new AdvanceMoney(['scenario' => AdvanceMoney::SCENARIO_CREATE_WITH_CONFIRM]);
        $model->user_id = $this->user_id;
        $model->amount = $this->amount;
        $model->comment = $this->description;
        $model->created = strtotime(Jdf::Convert_jalali_to_gregorian($this->date));
        $model->changed = strtotime(Jdf::Convert_jalali_to_gregorian($this->date));
        $model->creator_id = Yii::$app->user->id;
        $model->update_id = Yii::$app->user->id;
        $model->status = AdvanceMoney::STATUS_CONFIRM;
        $model->doc_id = $this->document_id;
        return $model->save();
    }

    public function behaviors()
    {
        return [
            'wageBehavior' => WageBehavior::class,
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
        ];
    }

    public function loadDefaultValues()
    {
        $this->date = Yii::$app->jdate->date("Y/m/d");
        $this->definite_id_from = Settings::get('AdvanceMoneyForm_DefaultDefiniteCreditor');
        $this->account_id_from = Settings::get('AdvanceMoneyForm_DefaultAccountCreditor');
    }

}