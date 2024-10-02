<?php

namespace hesabro\hris\models;

use common\models\BalanceDetailed;
use common\models\Settings;

class SalaryPeriodItemsQuery extends SalaryPeriodItemsQueryBase
{
    public function varianceAdvanceMoney()
    {
        return $this
            ->joinWith(['user.customer.oneAccount.balanceDetailed'])
            ->andWhere([BalanceDetailed::tableName() . '.definite_id' => Settings::get('m_debtor_advance_money')])
            ->andWhere(BalanceDetailed::tableName() . '.balance>0')
            ->andWhere(BalanceDetailed::tableName().'.`balance` <> '.SalaryPeriodItems::tableName().'.`advance_money`');
    }
}