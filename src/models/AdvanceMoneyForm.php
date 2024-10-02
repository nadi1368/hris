<?php

namespace hesabro\hris\models;

use hesabro\helpers\validators\DateValidator;
use common\behaviors\WageBehavior;
use common\validators\DefiniteValidator;
use common\models\Account;
use common\models\AccountDefinite;
use common\models\Document;
use common\models\Settings;
use Yii;

/**
 * @property AccountDefinite $definiteFrom
 * @property Account $accountFrom
 * @property Account $accountTo
 *
 * @mixin WageBehavior
 */
class AdvanceMoneyForm extends AdvanceMoneyFormBase
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'wageBehavior' => [
                'class' => WageBehavior::class,
            ]
        ]);
    }

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

    public function loadDefaultValues()
    {
        $this->date = Yii::$app->jdf->jdate("Y/m/d");
        $this->definite_id_from = Settings::get('AdvanceMoneyForm_DefaultDefiniteCreditor');
        $this->account_id_from = Settings::get('AdvanceMoneyForm_DefaultAccountCreditor');
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
}
