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
        return Yii::$app->urlManager->createAbsoluteUrl([Module::createUrl('advance-money-manage/index'), 'id' => $this->id]);
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
}