<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\OrganizationMember;
use hesabro\hris\models\OrganizationMemberSearch;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * EmployeeBranchController implements the CRUD actions for EmployeeBranch model.
 */
class OrganizationChartController extends Controller
{
    use AjaxValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['OrganizationChart/index'],
                        'actions' => ['index']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['OrganizationChart/public'],
                        'actions' => ['public']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['OrganizationChart/create'],
                        'actions' => ['create']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['OrganizationChart/update'],
                        'actions' => ['update', 'toggle-show-internal-number', 'toggle-show-job-tag']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['OrganizationChart/delete'],
                        'actions' => ['delete']
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all OrganizationMember models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationMemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all OrganizationMember models.
     * @return mixed
     */
    public function actionPublic()
    {
        $this->layout = 'panel';

        $searchModel = new OrganizationMemberSearch();
        $dataProvider = $searchModel->search([]);

        return $this->render('public', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Create new member
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrganizationMember();

        if ($model->load(Yii::$app->request->post())) {
            $response = ['success' => false, 'msg' => 'خطا در ثبت اطلاعات.'];

            if ($model->save()) {
                $response['success'] = true;
                $response['msg'] = 'اطلاعات با موفقیت ثبت شد.';

                return $this->asJson($response);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', ['model' => $model]);
    }

    /**
     * Update existing member
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $response = ['success' => false, 'msg' => 'خطا در ثبت اطلاعات.'];

            if ($model->save()) {
                $response['success'] = true;
                $response['msg'] = 'اطلاعات با موفقیت ثبت شد.';

                return $this->asJson($response);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', ['model' => $model]);
    }

    /**
     * Toggle member internal number show status
     * @return mixed
     */
    public function actionToggleShowInternalNumber($id)
    {
        $model = $this->findModel($id);

        $model->show_internal_number = !$model->show_internal_number;
        $model->save();

        return $this->asJson(['success' =>true, 'msg' => Yii::t("app", "Item created")]);
    }

    /**
     * Toggle member job tag show status
     * @return mixed
     */
    public function actionToggleShowJobTag($id)
    {
        $model = $this->findModel($id);

        $model->show_job_tag = !$model->show_job_tag;
        $model->save();

        return $this->asJson(['success' =>true, 'msg' => Yii::t("app", "Item created")]);
    }

    /**
     * Deletes an existing OrganizationMember model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (OrganizationMember::find()->where(['parent_id' => $id])->exists()) {
            throw new UnprocessableEntityHttpException(Yii::t('app', 'The memeber has child'));
        }

        $model->delete();

        return $this->redirect('index');
    }

    /**
     * Finds the OrganizationMember model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id id
     * @return OrganizationMember the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrganizationMember::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
