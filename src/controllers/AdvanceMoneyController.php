<?php

namespace hesabro\hris\controllers;

use common\models\Account;
use common\models\Document;
use common\models\Operation;
use common\models\AccountDefinite;
use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\AdvanceMoneyForm;
use hesabro\hris\Module;
use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class AdvanceMoneyController extends AdvanceMoneyBase
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
            throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
        }
        $operation = new Operation();
        $operation->setScenario(Operation::SCENARIO_PAYMENT_TO_CUSTOMER);
        $operation->price = (int)$model->amount;
        if ($operation->load(Yii::$app->request->post()) && $operation->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['success' => false, 'msg' => Module::t('module', 'Error In Save Info')];

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
                        'msg' => Module::t('module', 'Item Confirmed')
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
            'wageTypes' => Operation::itemAlias('WageType'),
            'getAccountUrl' => Url::to(['/account-definite/find', 'level' => AccountDefinite::LEVEL_DEFINITE, 'is_account' => 1]),
            'toList' => Account::itemAlias('Customer', null, $model->user->customer->id),
            'fromList' => Account::itemAlias('BankOrCashOrPublic')
        ]);
    }

    public function actionCreateWithConfirm($user_id)
    {
        $employee = $this->findModelEmployeeUser($user_id);
        $model = new AdvanceMoneyForm([
            'user_id' => $employee->user_id,
            'employee' => $employee,
            'account_id_to' => $employee->account_id,
        ]);

        if (!$model->canCreate()) {
            throw new ForbiddenHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->saveDocument();
                    $flag = $flag && $model->saveAdvanceMoney();
                    if ($flag) {
                        $result = [
                            'success' => true,
                            'msg' => Module::t('module', "Item Created")
                        ];
                        if(Yii::$app->request->post('TypeBtn', null) == 'document'){
                            $result['CallFunction'] = 'showModalAfterAjax';
                            $result['ModalUrl'] = Url::to(['/document/view', 'id' => $model->id]);
                        }
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                }
                return $this->asJson($result);
            }
        } else {
            $model->loadDefaultValues();
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_create-with-confirm', [
            'model' => $model,
            'accountDefiniteFind' => Url::to(['/account-definite/find', 'level' => AccountDefinite::LEVEL_DEFINITE]),
            'accountList' => Url::to(['/account/get-account'])
        ]);
    }
}
