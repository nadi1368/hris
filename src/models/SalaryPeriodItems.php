<?php

namespace hesabro\hris\models;

use common\models\UserPoints;
use console\job\SmsArrayJob;
use common\models\Year;
use Yii;
use hesabro\hris\Module;
use common\models\BalanceDetailed;
/**
 * Class SalaryPeriodItems
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class SalaryPeriodItems extends SalaryPeriodItemsBase
{
    /**
     * @return array|object|null
     */
    public function getYear()
    {
        if ($this->yearModel === null) {
            return Year::find()->byDate(Yii::$app->jdf->jdate("Y/m/d", $this->period->start_date))->one();
        }
        return $this->yearModel;
    }


    /**
     * @param $userId
     * @return int
     */
    public function getUserPoint($userId)
    {
        return UserPoints::countRequestPayment($userId);
    }

    /**
     * @param $account_id
     * @return int
     */
    public function getAdvanceMoney($account_id)
    {
        return BalanceDetailed::getBalance(Module::getInstance()->settings::get('m_debtor_advance_money'), $account_id);
    }

    /**
     * @return int
     * @throws \yii\web\NotFoundHttpException
     * مانده مساعده تا امروز
     */
    public function getAdvanceMoneyUntilThisMonth()
    {
        return (int)Module::getInstance()->balanceDailyClass::getBalanceDaily(Module::getInstance()->settings::get('m_debtor_advance_money'), $this->employee->account_id, Yii::$app->jdf->jdate("Y/m/d", $this->period->end_date));
    }

    public function sendSmsPayment()
    {
        $total_salary = number_format((float)($this->payment_salary - $this->advance_money));

        $message = "<آوا پرداز>";
        $message .= "\n\r";
        $message .= $this->user->fullName . ' عزیز';
        $message .= "\n\r";
        $message .= "مبلغ {$total_salary} ریال بابت حقوق {$this->period->title}  به حساب شما واریز گردید.";
        if ($this->count_point > 0) {
            $cost_point = number_format((float)$this->cost_point);
            $message .= "\n\r";
            $message .= "مبلغ {$cost_point} ریال ({$this->count_point}امتیاز) بابت امتیازات شما می باشد.";
        }
        if ($this->advance_money > 0) {
            $advance_money = number_format((float)$this->advance_money);
            $message .= "\n\r";
            $message .= "مبلغ {$advance_money} ریال بابت مساعده کسر گردید.";
        }
        $message .= "\n\r";

        Yii::$app->avaQueue->push(new SmsArrayJob([
            'receptors' => $this->user->username,
            'messages' => $message,
            'model_class' => self::class,
            'model_id' => $this->getPrimaryKey() ?? null
        ]));
    }
}