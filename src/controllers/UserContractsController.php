<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\UserContracts;
use Yii;
use yii\helpers\Html;
use common\models\Year;

class UserContractsController extends UserContractsBase
{
    /**
     * Creates a new UserContracts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($branch_id, $user_id, $contract_id, $start_date = null)
    {
        $modelUser = $this->findModelUser($branch_id, $user_id);
        $modelContract = $this->findModelContractTemplate($contract_id);
        $activeYear = Year::findOne(Year::getDefault());
        $model = new UserContracts([
            'start_date' => $start_date,
            'contract_id' => $modelContract->id,
            'branch_id' => $modelUser->branch_id,
            'user_id' => $modelUser->user_id,
            'daily_salary' => $activeYear->MIN_BASIC_SALARY,
            'right_to_housing' => $activeYear->COST_OF_HOUSE,
            'right_to_food' => $activeYear->COST_OF_FOOD,
            'right_to_child' => $activeYear->COST_OF_CHILDREN * ($modelUser->child_count ?? 0),
        ]);
        $model->setScenario(UserContracts::SCENARIO_CREATE);

        if ($model->load(Yii::$app->request->post())) {
            $model->setVariables();

            if ($model->save()) {
                $this->flash('success', Yii::t('app', 'Item Created'));
                return $this->redirect(['user-contracts/employee-contracts', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]);
            } else {
                if ($model->hasErrors()) {
                    $this->flash('error', Html::errorSummary($model));
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelUser' => $modelUser,
        ]);
    }
}