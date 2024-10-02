<?php

namespace hesabro\hris\models;

use common\behaviors\SendAutoCommentsBehavior;
use common\interfaces\SendAutoCommentInterface;
use common\models\CommentsType;
use common\models\SettingsAccount;
use hesabro\helpers\traits\CoreTrait;
use yii\helpers\Html;

class RequestLeave extends RequestLeaveBase implements SendAutoCommentInterface
{
    use CoreTrait;
    
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_LEAVE_REPORT,
                'title' => 'ثبت درخواست مرخصی',
                'scenarioValid' => [self::SCENARIO_CREATE]
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_LEAVE_REPORT,
                'title' => 'رد درخواست مرخصی',
                'scenarioValid' => [self::SCENARIO_REJECT],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_LEAVE_REPORT,
                'title' => 'تایید درخواست مرخصی',
                'scenarioValid' => [self::SCENARIO_CONFIRM],
                'callAfterUpdate' => true
            ],
        ]);
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
        return Yii::$app->urlManager->createAbsoluteUrl(['/request-leave/manage', 'RequestLeaveSearch[id]' => $this->id]);

    }

    /**
     * @return string
     */
    public function getContentMail(): string
    {
        $content = '';
        $type=self::itemAlias('Types', (int)$this->type);
        if ($this->getScenario() == self::SCENARIO_CREATE) {
            $content = Html::tag('p', "یک درخواست {$type} در سیستم ثبت شده است.");
            $content .= Html::tag('p', 'این درخواست توسط "' . $this->user->fullName . '" در سیستم ثبت شد.');
            $content .= Html::tag('p', 'توضیحات درخواست : "' . $this->description . '"');
            $content .= Html::tag('p', 'نوع درخواست : "' . self::itemAlias('Types', (int)$this->type) . '"');
            $content .= Html::tag('p', 'از تاریخ : "' . (in_array($this->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("l d F Y", $this->from_date) : Yii::$app->jdate->date("l d F Y  H:i", $this->from_date)) . '"');
            $content .= Html::tag('p', 'تا تاریخ : "' . (in_array($this->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("l d F Y", $this->to_date) : Yii::$app->jdate->date("l d F Y  H:i", $this->to_date)) . '"');
            $content .= Html::tag('p', 'بازه زمانی درخواست : "' . Yii::$app->formatter->asDuration($this->to_date - $this->from_date, '  و ') . '"');
        }
        if ($this->getScenario() == self::SCENARIO_REJECT) {
            $content = Html::tag('p', "متاسفانه درخواست {$type} شما مورد تایید قرار نگرفت.");
            $content .= Html::tag('p', 'این درخواست توسط "' . $this->update->fullName . '" رد شد.');
            $content .= Html::tag('p', 'توضیحات رد درخواست : "' . $this->rejectDescription . '"');
            $content .= Html::tag('p', 'نوع درخواست : "' . self::itemAlias('Types', (int)$this->type) . '"');
            $content .= Html::tag('p', 'از تاریخ : "' . (in_array($this->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("l d F Y", $this->from_date) : Yii::$app->jdate->date("l d F Y  H:i", $this->from_date)) . '"');
            $content .= Html::tag('p', 'تا تاریخ : "' . (in_array($this->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("l d F Y", $this->to_date) : Yii::$app->jdate->date("l d F Y  H:i", $this->to_date)) . '"');
        }
        if ($this->getScenario() == self::SCENARIO_CONFIRM) {
            $content = Html::tag('p', "درخواست {$type} شما مورد تایید قرار گرفت.");
            $content .= Html::tag('p', 'این درخواست توسط "' . $this->update->fullName . '" تایید شد.');
            $content .= Html::tag('p', 'نوع درخواست : "' . self::itemAlias('Types', (int)$this->type) . '"');
            $content .= Html::tag('p', 'از تاریخ : "' . (in_array($this->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("l d F Y", $this->from_date) : Yii::$app->jdate->date("l d F Y  H:i", $this->from_date)) . '"');
            $content .= Html::tag('p', 'تا تاریخ : "' . (in_array($this->type, array_keys(RequestLeave::itemAlias('TypesDaily'))) ? Yii::$app->jdate->date("l d F Y", $this->to_date) : Yii::$app->jdate->date("l d F Y  H:i", $this->to_date)) . '"');
        }

        return $content;
    }

    /**
     * @return bool
     */
    public function autoCommentCondition(): bool
    {
        return true;
    }
}