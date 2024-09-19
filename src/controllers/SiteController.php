<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeBranch;
use hesabro\hris\models\EmployeeBranchUser;
use common\models\Settings;
use common\models\UserUpload;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EmployeeBranchController implements the CRUD actions for EmployeeBranch model.
 */
class SiteController extends Controller
{
    use AjaxValidationTrait;
    public int $categorySetting = Settings::CAT_EMPLOYEE;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['EmployeeBranch/index'],
                            'actions' => ['index']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all EmployeeBranch models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     * Finds the EmployeeBranch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeBranch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeeBranch::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the EmployeeBranchUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $branchId
     * @param integer $userId
     * @return EmployeeBranchUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelUser($branchId, $userId)
    {
        if (($model = EmployeeBranchUser::find()->andWhere(['branch_id' => $branchId, 'user_id' => $userId])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModelUserUpload($id)
	{
		if (($model = UserUpload::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
	}

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
