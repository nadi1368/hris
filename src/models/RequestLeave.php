<?php

namespace hesabro\hris\models;

use common\behaviors\SendAutoCommentsBehavior;
use common\interfaces\SendAutoCommentInterface;
use common\models\CommentsType;
use common\models\SettingsAccount;
use hesabro\helpers\traits\CoreTrait;
use hesabro\hris\Module;
use Yii;
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
}