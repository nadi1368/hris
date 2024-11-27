<?php

namespace hesabro\hris\models;

use hesabro\helpers\behaviors\DocumentsDataBehavior;
use common\models\AccountDefinite;
use common\behaviors\MutexBehavior;
use common\models\Document;
use yii\helpers\Url;

class AdvanceMoney extends AdvanceMoneyBase
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => MutexBehavior::class,
            ],
            [
                'class' => DocumentsDataBehavior::class,
                'documentArrayField' => 'documentsArray',
                'documentClass' => Document::class,
                'documentViewUrl' => '/accounting/document/view'
            ],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMDebtor()
    {
        return $this->hasOne(AccountDefinite::class, ['id' => 'm_debtor_id']);
    }

    /**
     * @return bool
     */
    public function canTransferToMultiPay(): bool
    {
        return !$this->status_transfer_to_multi_pay && $this->canConfirm();
    }
    /**
     * @return bool
     */
    public function canUseFinno(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function confirm()
    {
        $this->status = self::STATUS_CONFIRM;
        return $this->save();
    }

    /**
     * @return bool
     */
    public function saveDocument()
    {
        $flag = true;
        $document = new Document();
        $document->process = Process::PRC_CONFIRM_ADVANCE_MONEY;
        $document->model_id = $this->id;
        $document->is_auto = 1;
        $document->date = $this->receipt_date;
        $document->description = "واریز وجه مساعده حقوق - رسید شماره " . $this->receipt_number;
        $flag = $flag && $document->save();

        $this->document = $document;

        /****************** بدهکار ******************/
        $flag = $flag && $document->setDetail($this->m_debtor_id, $this->employee->account_id, 0, $this->amount, $document->description);
        /****************** بستانکار ******************/
        $flag= $flag && $document->setDetailWithWage(SettingsAccount::get(SettingsAccount::MOIN11), $this->t_creditor_id, $this->amount, 0, $this->wage_type, $this->wage_amount, $document->description);

        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند شناسایی حقوق');
    }

    /**
     * @return array
     */
    public function getMDebtorUrl() : array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getTCreditorItems() : array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getDefiniteUrl(): string
    {
        return Url::to(['/account-definite/find', 'level' => 3, 'is_account' => 1]);
    }

    /**
     * @return string
     */
    public function getAccountUrl(): string
    {
        return Url::to(['/account/get-account']);
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getDefiniteSalaryTitle(bool $link = false): string
    {
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getAccountSalaryTitle(bool $link = false): string
    {
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getDefiniteInsuranceTitle(bool $link = false): string
    {
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getAccountInsuranceTitle(bool $link = false): string
    {
        return '';
    }
}