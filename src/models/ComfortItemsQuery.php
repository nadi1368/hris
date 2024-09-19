<?php

namespace hesabro\hris\models;

use common\components\jdf\Jdf;
use common\models\Comments;
use common\models\Year;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the ActiveQuery class for [[ComfortItems]].
 *
 * @see ComfortItems
 */
class ComfortItemsQuery extends \yii\db\ActiveQuery
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
     * @return ComfortItemsQuery
     */
    public function byStatus(int $status): ComfortItemsQuery
    {
        return $this->andWhere(['status' => $status]);
    }
    /**
     * @return ComfortItemsQuery
     */
    public function confirm(): ComfortItemsQuery
    {
        return $this->andWhere(['status' => ComfortItems::STATUS_CONFIRM]);
    }

    /**
     * @return ComfortItemsQuery
     */
    public function waiting(): ComfortItemsQuery
    {
        return $this->andWhere(['status' => ComfortItems::STATUS_WAIT_CONFIRM]);
    }

    /**
     * @param int $userId
     * @return ComfortItemsQuery
     */
    public function byUser(int $userId): ComfortItemsQuery
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param int $comfortId
     * @return ComfortItemsQuery
     */
    public function byComfort(int $comfortId): ComfortItemsQuery
    {
        return $this->andWhere(['comfort_id' => $comfortId]);
    }

    /**
     * @param string $time
     * @return ComfortItemsQuery
     */
    public function thisYear(string $time = ''): ComfortItemsQuery
    {
        $time = Jdf::getStartAndEndOfCurrentYear($time);
        return $this->andWhere(['between', 'created', $time['start'], $time['end']]);
    }
    /**
     * @param string $time
     * @return ComfortItemsQuery
     */
    public function thisMonth(string $time = ''): ComfortItemsQuery
    {
        $time = Jdf::getStartAndEndOfCurrentMonth($time);
        return $this->andWhere(['between', 'created', $time[0], $time[1]]);
    }

    /**
     * @param $startTime
     * @param $endTime
     * @return ComfortItemsQuery
     */
    public function byDate(int $startTime,int $endTime): ComfortItemsQuery
    {
        return $this->andWhere(['between', 'created', $startTime, $endTime]);
    }

    public function withCommentsCount(): ComfortItemsQuery
    {
        $query = new Query();
        $query->from(Comments::tableName())
            ->select(new Expression('count(*)'))
            ->andWhere(new Expression(Comments::tableName() . '.`class_name` = "' . addslashes(ComfortItems::class) . '"'))
            ->andWhere(new Expression(Comments::tableName() . '.`class_id` = ' . ComfortItems::tableName() . '.`id`'));

        $this->select([
            ComfortItems::tableName() . '.*',
            new Expression('(' . $query->createCommand()->getRawSql() . ') AS `comments_count`')
        ]);

        return $this;
    }
}
