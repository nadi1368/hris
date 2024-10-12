<?php

namespace hesabro\hris\controllers;

use common\models\Year;
use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\Module;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SalaryPeriodController extends SalaryPeriodBase
{
    /**
     * @param $workshop_id
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateReward($workshop_id)
    {
        $model = $this->findModelWorkShop($workshop_id);

        if (!$model->canCreateReward()) {
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation"));
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $startAndEndOfCurrentYear = Yii::$app->jdf::getStartAndEndOfCurrentYear(Year::getDefault('endTime'));
            $salaryPeriod = new SalaryPeriod([
                'scenario' => SalaryPeriod::SCENARIO_CREATE_REWARD,
                'workshop_id' => $model->id,
                'title' => 'عیدی و پاداش ' . Yii::$app->jdf->jdate("Y", $startAndEndOfCurrentYear['start']),
                'kind' => SalaryPeriod::KIND_REWARD,
                'start_date' => $startAndEndOfCurrentYear['start'],
                'end_date' => $startAndEndOfCurrentYear['end'],
            ]);
            $flag = $salaryPeriod->save();
            if ($flag) {
                $transaction->commit();
                $this->flash("success", Module::t('module', 'Item Created'));
                return $this->redirect(['reward-period-items/index', 'id' => $salaryPeriod->id]);

            } else {
                $transaction->rollBack();
                $this->flash("warning", Module::t('module', "Error In Save Info"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            $this->flash('warning', $e->getMessage());
        }
        return $this->redirect(['salary-period/index', 'SalaryPeriodSearch[workshop_id]' => $workshop_id]);
    }

    /**
     * @param $workshop_id
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateYear($workshop_id)
    {
        $model = $this->findModelWorkShop($workshop_id);
        if (!$model->canCreateYear()) {
            throw new HttpException(400, Module::t('module', "It is not possible to perform this operation"));
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $salaryPeriod = new SalaryPeriod([
                'scenario' => SalaryPeriod::SCENARIO_CREATE_YEAR,
                'workshop_id' => $model->id,
                'title' => 'سنوات ' . Year::getDefault('title'),
                'kind' => SalaryPeriod::KIND_YEAR,
                'start_date' => Year::getDefault('start'),
                'end_date' => Year::getDefault('end'),
            ]);
            $flag = $salaryPeriod->save();
            if ($flag) {
                $transaction->commit();
                $this->flash("success", Module::t('module', 'Item Created'));
                return $this->redirect(['year-period-items/index', 'id' => $salaryPeriod->id]);

            } else {
                $transaction->rollBack();
                $this->flash("warning", Module::t('module', "Error In Save Info"));
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            $transaction->rollBack();
            $this->flash('warning', $e->getMessage());
        }
        return $this->redirect(['salary-period/index', 'SalaryPeriodSearch[workshop_id]' => $workshop_id]);
    }
}
