<?php

namespace hesabro\hris\models;

use common\models\Comments;
use yii\db\Expression;
use yii\db\Query;


class ComfortItemsQuery extends ComfortItemsQueryBase
{
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
