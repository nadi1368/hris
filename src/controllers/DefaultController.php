<?php

namespace hesabro\hris\controllers;

use backend\models\YearSearch;
use common\models\Year;
use hesabro\hris\Module;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends DefaultBase
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->layout = Module::getInstance()->layout;
    }

    /**
     * Lists all Year models.
     * @return mixed
     */
    public function actionYearSetting()
    {
        $searchModel = new YearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('year-setting', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionUpdateYearSetting($id)
    {
        $model = $this->findModelYear($id);
        $model->setScenario(Year::SCENARIO_UPDATE_SALARY_JSON);
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Created")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            }
            return $this->asJson($result);
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_update-salary', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Year model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Year the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelYear($id)
    {
        if (($model = Year::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }
}