<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeChild;
use hesabro\hris\models\EmployeeExperience;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ProfileController extends Controller
{
    use AjaxValidationTrait;

    public $layout = 'panel';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['@']
                        ]
                    ]
            ]
        ];
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $model = $this->findEmployeeBranchUser(Yii::$app->user->getId());
        $model->setScenario(EmployeeBranchUser::SCENARIO_UPDATE_PROFILE);

        $insuranceData = $model->getInsuranceData();

        $model->employee_address = $insuranceData['employee_address'];
        $model->first_name = $insuranceData['first_name'];
        $model->last_name = $insuranceData['last_name'];
        $model->nationalCode = $insuranceData['nationalCode'];

        if ($request->isPost) {
            $updateAvatar = true;
            $user = Module::getInstance()->user::findOne(Yii::$app->user->getId());
            if ($avatar = UploadedFile::getInstance($model, 'avatar')) {
                $user->scenario = Module::getInstance()->user::SCENARIO_UPDATE_AVATAR;
                $user->avatar = $avatar;
                $updateAvatar = $user->save(false);
            }

            $model->children = EmployeeChild::createMultiple(EmployeeChild::class);
            EmployeeChild::loadMultiple($model->children, $request->post());
            $valid = EmployeeChild::validateMultiple($model->children);

            $model->experiences = EmployeeExperience::createMultiple(EmployeeExperience::class);
            EmployeeExperience::loadMultiple($model->experiences, $request->post());
            $valid = $valid && EmployeeExperience::validateMultiple($model->experiences);

            $updateProfile = $valid && $updateAvatar;

            if (!$model->isConfirmed && $updateProfile) {
                $updateProfile = $model->load($request->post()) && $model->save();
            }

            if ($model->isConfirmed && $updateProfile) {
                $updateProfile = $model->load($request->post()) && $model->saveToPending();
            }

            if (!$updateAvatar) {
                $model->addError('avatar', $user->getFirstError('avatar') ?: Module::t('module', 'Error In Save Information, Please Try Again'));
            }

            if ($updateProfile) {
                Yii::$app->getSession()->setFlash('success', Module::t('module', 'Item Updated'));
                return $this->redirect(['profile/update']);
            }
        }

        if ($request->isGet) {
            $model->children = $model->getChildrenWithPending();
            $model->experiences = $model->getExperiencesWithPending();
        }

        if (!count($model->children)) {
            $model->children = [new EmployeeChild(['isNewRecord' => true])];
        }

        if (!count($model->experiences)) {
            $model->experiences = [new EmployeeExperience(['isNewRecord' => true])];
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionSeenReject()
    {
        $model = $this->findEmployeeBranchUser(Yii::$app->user->getId());
        $seen = $model->seenRejectUpdate();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $seen,
            'msg' => Module::t('module', $seen ? 'Item Updated' : 'Can Not Update')
        ];
    }

    private function findEmployeeBranchUser($userId)
    {
        $model = EmployeeBranchUser::find()->where(['user_id' => $userId])->with(['user'])->one();

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $model;
    }


}
