<?php

namespace hesabro\hris\models;

use common\models\Comments;
use hesabro\hris\Module;
use Yii;
use yii\db\ActiveQuery;

class ComfortItems extends ComfortItemsBase
{
    public function getComments(): ActiveQuery
    {
        return $this->hasMany(Comments::class, ['class_id' => 'id'])->andWhere(['class_name' => self::class]);
    }

    public function createComment(Comments $comment): bool
    {
        if (!is_array($comment->owner) || !count($comment->owner)) {

            return true;
        }

        $parent = Comments::find()->isParent()->where([
            'class_name' => self::class,
            'class_id' => $this->id
        ])->limit(1)->one();

        $formName = (new ComfortItemsSearch())->formName();
        $comment->is_duty = true;
        $comment->parent_id = $parent?->id ?: 0;
        $comment->type = Comments::TYPE_PRIVATE;
        $comment->status = Comments::STATUS_ACTIVE;
        $comment->class_name = self::class;
        $comment->class_id = $this->id;
        $comment->creator_id = Yii::$app->user->identity->getId();
        $comment->link = Yii::$app->urlManager->createAbsoluteUrl([Module::createUrl('comfort-items/index'), $formName . '[id]' => $this->id]);
        $comment->title = implode(' ', [
            Module::t('module', 'Refer'),
            Module::t('module', 'Comfort Items'),
            $this->comfort->title,
            $this->user->fullName
        ]);

        return $comment->save() && $comment->saveInbox();
    }
}
