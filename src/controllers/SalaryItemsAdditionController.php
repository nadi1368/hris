<?php

namespace hesabro\hris\controllers;

use backend\models\RejectForm;
use backend\models\UploadExcelSearch;
use common\models\UploadExcel;
use hesabro\hris\Module;
use Yii;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SalaryItemsAdditionController extends SalaryItemsAdditionBase
{
    /**
     * @param $id
     * @param $status
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $form = new RejectForm();
        if ($model->canReject()) {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->reject($form->description);
                    if ($flag) {
                        $transaction->commit();
                        $result = [
                            'success' => true,
                            'msg' => Module::t('module', 'Item Rejected')
                        ];
                    } else {
                        $transaction->rollBack();
                        $result = [
                            'success' => false,
                            'msg' => Html::errorSummary($model)
                        ];
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $result = [
                        'success' => false,
                        'msg' => $e->getMessage()
                    ];
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $result;
            }
        } else {
            throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
        }

        $this->performAjaxValidation($form);

        return $this->renderAjax('_form_reject', [
            'model' => $form,
        ]);
    }

    /**
     * @return string
     */
    public function actionListCsvSalaryNonCash()
    {
        $searchModel = new UploadExcelSearch(['type' => [UploadExcel::TYPE_SALARY_NON_CASH]]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list-csv-salary-non-cash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUploadSalaryNonCash()
    {
        $uploadForm = new UploadExcel(['scenario' => UploadExcel::SCENARIO_UPLOAD_SALARY_NON_CASH, 'type' => UploadExcel::TYPE_SALARY_NON_CASH, 'month' => Yii::$app->jdf->jdate("m", strtotime('-10 DAY'))]);
        if ($uploadForm->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $uploadForm->date = Yii::$app->jdf->jdate("Y/") . $uploadForm->month . '/01';
                if ($uploadForm->save()) {
                    $transaction->commit();
                    $this->flash('success', 'آپلود با موفقیت انجام شد.');
                    return $this->redirect(['insert-salary-non-cash', 'csv_id' => $uploadForm->id]);

                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Module::t('module', "Error In Save Info"));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage() . $e->getTraceAsString());
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        }
        return $this->render('upload-salary-non-cash', [
            'uploadForm' => $uploadForm,
        ]);
    }

    /**
     * Finds the UploadExcel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UploadExcel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModelUploadFile(int $id)
    {
        if (($model = UploadExcel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }
}
