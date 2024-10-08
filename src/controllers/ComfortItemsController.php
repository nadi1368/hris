<?php

namespace hesabro\hris\controllers;

use common\models\Comments;
use hesabro\hris\models\ComfortItems;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ComfortItemsController extends ComfortItemsBase
{
    /**
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(ComfortItems::SCENARIO_CONFIRM);
        $comment = new Comments();
        if (!$model->canConfirm()) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = ComfortItems::STATUS_CONFIRM;
                $flag = $model->save(false);
                if($flag && $model->saveAdvanceMoney)
                {
                    $flag = $model->saveAdvanceMoney();
                }

                $flag = $flag && $model->createSalaryItemAddition();

                if ($flag && $comment->load(Yii::$app->request->post())) {
                    $flag = $model->createComment($comment);
                }

                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Yii::t("app", "Item Confirmed")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
            }

            return $this->asJson($result);
        }

        $this->performAjaxValidation($model);
        if ($comment->hasErrors()) {
            $this->performAjaxValidation($comment);
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'comment' => $comment
        ]);
    }

    public function actionRefer($id)
    {
        $model = $this->findModel($id);
        $comment = new Comments();

        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];

        if ($comment->load(Yii::$app->request->post()) && $comment->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($model->createComment($comment)) {
                    $result = [
                        'success' => true,
                        'msg' => Yii::t('app', 'Item Referred')
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
            }

            return $this->asJson($result);
        }

        $this->performAjaxValidation($comment);
        return $this->renderAjax('_refer', [
            'model' => $model,
            'comment' => $comment
        ]);
    }
}
