<?php

namespace hesabro\hris\models;

use common\models\AccountDefinite;
use common\models\CommentsType;
use common\behaviors\SendAutoCommentsBehavior;
use common\interfaces\SendAutoCommentInterface;
use hesabro\hris\Module;
use Yii;
use yii\helpers\Html;

class AdvanceMoney extends AdvanceMoneyBase implements SendAutoCommentInterface
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_ADVANCE_MONEY,
                'title' => 'ثبت درخواست مساعده',
                'scenarioValid' => [self::SCENARIO_CREATE, self::SCENARIO_CREATE_AUTO]
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_ADVANCE_MONEY_REJECT,
                'title' => 'رد درخواست مساعده',
                'scenarioValid' => [self::SCENARIO_REJECT],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_ADVANCE_MONEY_CONFIRM,
                'title' => 'تایید درخواست مساعده',
                'scenarioValid' => [self::SCENARIO_CONFIRM],
                'callAfterUpdate' => true
            ],
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
        return Yii::$app->urlManager->createAbsoluteUrl([Module::createUrl('employee-advance-money/index'), 'id' => $this->id]);
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
}