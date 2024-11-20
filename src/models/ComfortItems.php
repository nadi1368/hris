<?php

namespace hesabro\hris\models;

use common\behaviors\CdnUploadFileBehavior;
use common\behaviors\SendAutoCommentsBehavior;
use common\interfaces\SendAutoCommentInterface;
use common\models\Comments;
use common\models\CommentsType;
use hesabro\hris\Module;
use Yii;
use yii\db\ActiveQuery;

class ComfortItems extends ComfortItemsBase implements SendAutoCommentInterface
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_COMFORT,
                'title' => 'ثبت درخواست امکانات رفاهی',
                'scenarioValid' => [self::SCENARIO_CREATE, self::SCENARIO_LOAN_CREATE]
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_COMFORT_CONFIRM,
                'title' => 'تایید درخواست امکانات رفاهی',
                'scenarioValid' => [self::SCENARIO_CONFIRM],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::REQUEST_COMFORT_REJECT,
                'title' => 'رد درخواست امکانات رفاهی',
                'scenarioValid' => [self::SCENARIO_REJECT],
                'callAfterUpdate' => true
            ]
        ]);
    }

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
