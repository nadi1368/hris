<?php

namespace hesabro\hris\models;

use hesabro\helpers\validators\PersianValidator;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Faq]].
 *
 * @see Faq
 */
class ContentQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Content[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Content|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', Content::tableName() . '.status', Content::STATUS_DELETED]);
    }

    public function byType($type)
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * Query by client
     */
    public function byClientAccess($clientID): self
    {
        return $this->andWhere([
            'OR',
            new Expression("JSON_CONTAINS(". Content::tableName() .".additional_data, JSON_QUOTE('$clientID'), '$.include_client_ids')"),
            new Expression("JSON_CONTAINS(". Content::tableName() .".additional_data, JSON_QUOTE('*'), '$.include_client_ids')"),
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.include_client_ids') IS NULL")
        ])->andWhere([
            'OR',
            new Expression("(NOT JSON_CONTAINS(" . Content::tableName() . ".additional_data, JSON_QUOTE('$clientID'), '$.exclude_client_ids'))"),
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.exclude_client_ids') IS NULL")
        ]);
    }

    public function byCustomJobTags($tags): self
    {
        return $this->andWhere(['OR',
            ['JSON_OVERLAPS(additional_data->"$.custom_salary_insurance", \'' . json_encode($tags) . '\')' => 1],
            ['JSON_LENGTH(JSON_EXTRACT(additional_data, "$.custom_job_tags"))' => 0]
        ]);
    }

    public function byCustomUserId($userId): self
    {
        return $this->andWhere(['OR',
            ['JSON_CONTAINS(additional_data->"$.custom_user_ids", JSON_QUOTE("' . $userId . '"))' => 1],
            ['JSON_LENGTH(JSON_EXTRACT(additional_data, "$.custom_user_ids"))' => 0]
        ]);
    }

    public function byShowStartAt(int $date): self
    {
        $this->andWhere(['OR',
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_start_at') <= $date"),
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_start_at') IS NULL")
        ]);

        return $this;
    }

    public function byShowEndAt(int $date): self
    {
        $this->andWhere(['OR',
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_end_at') >= $date"),
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_end_at') IS NULL")
        ]);

        return $this;
    }

    public function byIsBanner(bool $isBanner): self
    {
        if ($isBanner) {
            $this->andWhere([
                'OR',
                new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_start_at') IS NOT NULL"),
                new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_end_at') IS NOT NULL"),
            ]);

            return $this;
        }

        $this->andWhere([
            'AND',
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_start_at') IS NULL"),
            new Expression("JSON_EXTRACT(". Content::tableName() .".additional_data, '$.show_end_at') IS NULL"),
        ]);

        return $this;
    }

    public function byScatteredSearch(string $query): self
    {
        $likeCondition = null;
        $searchKey = '';
        $searchKeys = explode(' ', $query);
        $likeCondition[0] = 'AND';

        foreach ($searchKeys as $searchKey) {
            $searchKey = PersianValidator::replaceChar($searchKey);
            if ($searchKey) {
                $likeCondition[] = ['OR',
                    ['like', 'title', $searchKey],
                    ['like', 'description', $searchKey],
                    ['like', "JSON_EXTRACT(additional_data, '$.clauses')", $searchKey]
                ];
            }
        }

        return $this->andWhere($likeCondition);
    }
}
