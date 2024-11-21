<?php

namespace hesabro\hris\controllers;

use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class EmployeeBranchUserController extends Controller
{
    use AjaxValidationTrait;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['EmployeeBranchUser/actions', 'superadmin'],
                            'actions' => ['create']
                        ],
                    ]
            ]
        ];
    }

    public function actionCreate()
    {
        $model = new EmployeeBranchUser(['scenario' => EmployeeBranchUser::SCENARIO_CREATE]);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            $assignment = Yii::createObject([
                'class' => Module::getInstance()->assignment,
                'user_id' => $model->user_id
            ]);

            $assignment->items = array_diff($assignment->items, Module::getInstance()->hiringDetachRoles);
            $assignment->items = array_unique(array_merge($assignment->items, Module::getInstance()->hiringAttachRoles));
            try {
                if ($model->save(false) && $assignment->updateAssignments()) {
                    $transaction->commit();
                    return $this->asJson([
                        'success' => true,
                        'msg' => Module::t('module', 'Item Created')
                    ]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), __METHOD__ . ':' . __LINE__);
            }

            return $this->asJson([
                'success' => false,
                'msg' => Module::t('module', 'Error In Save Info')
            ]);
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model
        ]);
    }
}