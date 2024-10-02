<?php

namespace hesabro\hris\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the ActiveQuery class for [[ComfortItems]].
 *
 * @see ComfortItems
 */
class ComfortItemsQueryBase extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ComfortItems[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ComfortItems|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->andOnCondition(['<>', ComfortItems::tableName() . '.status', ComfortItems::STATUS_DELETED]);
    }


    public function notReject()
    {
        return $this->onCondition(['NOT IN', ComfortItems::tableName() . '.status', [ComfortItems::STATUS_REJECT, ComfortItems::STATUS_DELETED]]);
    }

    /**
     * @param int $status
     * @return self
     */
    public function byStatus(int $status): self
    {
        return $this->andWhere(['status' => $status]);
    }
    /**
     * @return self
     */
    public function confirm(): self
    {
        return $this->andWhere(['status' => ComfortItems::STATUS_CONFIRM]);
    }

    /**
     * @return ComfortItemsQuery
     */
    public function waiting(): self
    {
        return $this->andWhere(['status' => ComfortItems::STATUS_WAIT_CONFIRM]);
    }

    /**
     * @param int $userId
     * @return self
     */
    public function byUser(int $userId): self
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param int $comfortId
     * @return self
     */
    public function byComfort(int $comfortId): self
    {
        return $this->andWhere(['comfort_id' => $comfortId]);
    }

    /**
     * @param string $time
     * @return self
     */
    public function thisYear(string $time = ''): self
    {
        $time = Yii::$app->jdf::getStartAndEndOfCurrentYear($time);
        return $this->andWhere(['between', 'created', $time['start'], $time['end']]);
    }
    /**
     * @param string $time
     * @return self
     */
    public function thisMonth(string $time = ''): self
    {
        $time = Yii::$app->jdf::getStartAndEndOfCurrentMonth($time);
        return $this->andWhere(['between', 'created', $time[0], $time[1]]);
    }

    /**
     * @param int $startTime
     * @param int $endTime
     * @return self
     */
    public function byDate(int $startTime,int $endTime): self
    {
        return $this->andWhere(['between', 'created', $startTime, $endTime]);
    }
}
