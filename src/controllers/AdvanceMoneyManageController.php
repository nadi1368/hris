<?php

namespace hesabro\hris\controllers;

use common\models\Document;
use common\models\Operation;
use hesabro\hris\models\AdvanceMoney;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class AdvanceMoneyManageController extends AdvanceMoneyManageBase
{
    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AdvanceMoney::SCENARIO_CONFIRM);
        if (!$model->canConfirm()) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $operation = new Operation();
        $operation->setScenario(Operation::SCENARIO_PAYMENT_TO_CUSTOMER);
        $operation->price = (int)$model->amount;
        if ($operation->load(Yii::$app->request->post()) && $operation->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['success' => false, 'msg' => Yii::t('app', 'Error In Save Info')];

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $operation->saveDocument(Document::TYPE_PAYMENT_TO_CUSTOMER, $operation->serial);
                $model->doc_id = $operation->document->id;
                $model->status = AdvanceMoney::STATUS_CONFIRM;
                $flag = $flag && $model->save();

                if ($flag) {
                    $transaction->commit();
                    $response = [
                        'success' => true,
                        'msg' => Yii::t('app', 'Item Confirmed')
                    ];
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
                $response = [
                    'success' => false,
                    'msg' => $e->getMessage()
                ];
            }


            return $response;
        }
        $this->performAjaxValidation($model);
        $operation->date = Yii::$app->jdf->jdate("Y/m/d");
        $operation->des = $model->comment . " - درخواست مساعده #" . $model->id;
        return $this->renderAjax('_confirm', [
            'model' => $model,
            'operation' => $operation,
        ]);
    }
}
