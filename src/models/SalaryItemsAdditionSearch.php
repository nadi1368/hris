<?php

namespace hesabro\hris\models;

use backend\models\AuthAssignment;
use hesabro\hris\Module;
use Yii;

class SalaryItemsAdditionSearch extends SalaryItemsAdditionSearchBase
{
    public function searchReportLeaveLineChart($params)
    {
        $employees = Module::getInstance()->user::find()
            ->select(['id', 'username', 'first_name', 'last_name'])
            ->joinWith('authAssignment')
            ->andWhere([AuthAssignment::tableName() . '.item_name' => 'employee'])
            ->all();

        $kindHourly = SalaryItemsAddition::KIND_LEAVE_HOURLY;
        $kindColumn = SalaryItemsAddition::tableName() . '.kind';
        $query = SalaryItemsAddition::find()
            ->select([
                SalaryItemsAddition::tableName() . '.user_id',
                'year' => 'pyear(FROM_UNIXTIME(`from_date`))',
                'month' => 'pmonth(FROM_UNIXTIME(`from_date`))',
                'total' => "SUM(CASE WHEN $kindColumn=$kindHourly THEN (`second`/(8*60)) ELSE ((`to_date`-`from_date`)/(24*60*60)) END)"
            ])
            ->andWhere([$kindColumn => [SalaryItemsAddition::KIND_LEAVE_HOURLY, SalaryItemsAddition::KIND_LEAVE_DAILY]])
            ->andWhere([SalaryItemsAddition::tableName() . '.status' => SalaryItemsAddition::STATUS_CONFIRM])
            ->andWhere(['IN', SalaryItemsAddition::tableName() . '.user_id', array_map(fn(User $user) => $user->id, $employees)])
            ->groupBy([SalaryItemsAddition::tableName() .'.user_id', 'month'])
            ->orderBy([SalaryItemsAddition::tableName() .'.user_id' => SORT_ASC, 'year' => SORT_ASC, 'month' => SORT_ASC]);

        $from = $this->from_date ? Yii::$app->jdf::jalaliToTimestamp($this->from_date,"Y/m/d") : strtotime('-1 year');
        $to = $this->to_date ? Yii::$app->jdf::jalaliToTimestamp($this->to_date,"Y/m/d") : time();

        $query->andFilterWhere(['>=', 'from_date', $from]);
        $query->andFilterWhere(['<=', 'from_date', $to]);

        $items = $query->all();

        $chartData = [
            'names' => [Module::t('module', 'Month')]
        ];

        foreach ($items as $item) {
            $chartData['names'][$item->user_id] = $item->user->fullName;
        }
        ksort($chartData['names']);

        $filterTimestamp = $from;
        do {
            [$y, $m] = explode('/', Yii::$app->jdf->jdate('Y/m', $filterTimestamp));
            $key = (int) ($y.$m);
            $chartData[$key] = ["$y/$m"];

            foreach ($chartData['names'] as $userId => $nameName) {
                if ($userId <= 0) {
                    continue;
                }
                $userData = current(array_filter($items, fn($i) => $i->user_id == $userId && $i->year == $y && $i->month == $m));
                $chartData[$key][$userId] = (float) ($userData ? $userData->total : 0);
            }

            $filterTimestamp = Yii::$app->jdf::jalaliToTimestamp(Yii::$app->jdf->add_month($filterTimestamp), 'Y/m/d');
        } while ($filterTimestamp < $to);

        return array_values(array_map(fn(array $data) => array_values($data), $chartData));
    }
}