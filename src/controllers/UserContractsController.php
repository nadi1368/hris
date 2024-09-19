<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\UserContracts;
use hesabro\hris\models\UserContractsSearch;
use common\models\Settings;
use common\models\Year;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserContractsController implements the CRUD actions for UserContracts model.
 */
class UserContractsController extends Controller
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
						'roles' => ['UserContracts/index'],
						'actions' => ['index', 'employee-contracts', 'view-my-contract'],
					],
					[
						'allow' => true,
						'roles' => ['UserContracts/create'],
						'actions' => ['create', 'pre-create', 'confirm'],
					],
					[
						'allow' => true,
						'roles' => ['UserContracts/update'],
						'actions' => ['update', 'change-shelf'],
					],
					[
						'allow' => true,
						'roles' => ['UserContracts/delete'],
						'actions' => ['delete'],
					],
					[
						'allow' => true,
						'roles' => ['UserContracts/view'],
						'actions' => ['view'],
					],
					[
						'allow' => true,
						'roles' => ['UserContracts/update'],
						'actions' => ['un-confirm'],
					],
					[
						'allow' => true,
						'roles' => ['UserContracts/my'],
						'actions' => ['my-contracts', 'view-my-contract'],
					]
				],
			],
		];
	}

	public function actionEmployeeContracts($branch_id, $user_id)
	{
		$model = $this->findModelUser($branch_id, $user_id);
		$searchModel = new UserContractsSearch(['user_id' => $user_id]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('my', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all UserContracts models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new UserContractsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single UserContracts model.
	 *
	 * @param int $id شناسه
	 *
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id, $print = false)
	{
		if ($print) {
			$this->layout = 'print';
		}
		$model = $this->findModel($id);
		$model->checkVariables();

		if ($print) {
			return $this->render('_view', [
				'model' => $model,
				'print' => $print
			]);
		}
		return $this->renderAjax('_view', [
			'model' => $model,
			'print' => $print
		]);
	}

	public function actionConfirm($id)
	{
		$model = $this->findModel($id);
		$model->checkVariables();

		if (!$model->canConfirm()) {
			throw new ForbiddenHttpException('امکان تایید قرارداد وجود ندارد.');
		}

		if ($model->confirm()) {
			$result = [
				'status' => true,
				'message' => 'قرارداد با موفقیت تایید شد.'
			];
		} else {
			$result = [
				'status' => false,
				'message' => $model->err_msg ?: Yii::t('app', 'Error In Save Info'),
			];
		}

		Yii::$app->response->format = Response::FORMAT_JSON;
		return $result;
	}

	/**
	 * Creates a new UserContracts model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionPreCreate($branch_id, $user_id)
	{
		$modelUser = $this->findModelUser($branch_id, $user_id);
		$model = new UserContracts(['branch_id' => $modelUser->branch_id, 'user_id' => $modelUser->user_id]);
		$model->setScenario(UserContracts::SCENARIO_PRE_CREATE);

		if ($model->load(Yii::$app->request->post())) {
			$result = [
				'success' => true,
				'msg' => Yii::t("app", 'Item Created'),
				'redirect' => true,
				'url' => Url::to(['create', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id, 'contract_id' => $model->contract_id]),
			];

			Yii::$app->response->format = Response::FORMAT_JSON;
			return $result;
		}

		$this->performAjaxValidation($model);
		return $this->renderAjax('_form_pre_create', [
			'model' => $model,
		]);
	}

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

	/**
	 * Updates an existing UserContracts model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id شناسه
	 *
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$model->setScenario(UserContracts::SCENARIO_UPDATE);
		$modelUser = $this->findModelUser($model->branch_id, $model->user_id);
		if (!$model->canUpdate()) {
			$this->flash('danger', Yii::t("app", "Can Not Update"));
			return $this->redirect(['index']);
		}
		if ($model->load(Yii::$app->request->post())) {
			$model->setVariables();

			if ($model->save()) {
				$this->flash('success', Yii::t('app', 'Item Updated'));
				return $this->redirect(['user-contracts/employee-contracts', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]);
			} else {
				if ($model->hasErrors()) {
					$this->flash('error', Html::errorSummary($model));
				}
			}
		}

		return $this->render('update', [
			'model' => $model,
			'modelUser' => $modelUser,
		]);
	}

	/**
	 * Deletes an existing UserContracts model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id شناسه
	 *
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id)
	{
		$model = $this->findModel($id);

		$transaction = \Yii::$app->db->beginTransaction();
		try {
			if ($model->softDelete()) {
				$transaction->commit();
				$this->flash('success', Yii::t("app", "Item Deleted"));
			} else {
				$this->flash('danger', $model->err_msg ?: Yii::t("app", "Error In Save Info"));
				$transaction->rollBack();
			}
		} catch (\Exception $e) {
			$transaction->rollBack();
			Yii::error($e->getMessage() . $e->getTraceAsString(),  __METHOD__ . ':' . __LINE__);
			$this->flash('warning', $e->getMessage());
		}


		return $this->redirect(Yii::$app->request->referrer);
	}

	public function actionUnConfirm($id)
	{
		$model = $this->findModel($id);

		if (!$model->canUnConfirm()) {
			throw new ForbiddenHttpException('امکان برگشت تایید قرارداد وجود ندارد.');
		}

		if ($model->unConfirm()) {
			$this->flash('success', Yii::t("app", "Item UnConfirmed"));
		} else {
			$this->flash('danger', $model->err_msg ?: Yii::t("app", "Error In Save Info"));
		}

		return $this->redirect(['index']);
	}

	public function actionChangeShelf($id)
	{
		$response = ['success' => false, 'data' => '', 'msg' => 'خطا در ثبت اطلاعات.'];

		$model = $this->findModel($id);
		$model->setScenario(UserContracts::SCENARIO_CHANGE_SHELF);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$response = [
				'success' => true,
				'msg' => Yii::t('app', 'Item Updated')
			];

			Yii::$app->response->format = Response::FORMAT_JSON;
			return $response;
		}

		$this->performAjaxValidation($model);
		return $this->renderAjax('_change_shelf_form', [
			'model' => $model,
		]);
	}

	/**
	 * Lists all UserContracts models.
	 *
	 * @return mixed
	 */
	public function actionMyContracts()
	{
		$this->layout = 'panel';

		$searchModel = new UserContractsSearch(['user_id' => Yii::$app->user->identity->id]);
		$dataProvider = $searchModel->search([]);

		return $this->render('my_contracts', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single UserContracts model.
	 *
	 * @param int $id
	 *
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionViewMyContract($id, $print = false)
	{
		if ($print) {
			$this->layout = 'print';
		}

		$model = $this->findUserContract($id);

		$model->checkVariables();

		if ($print) {
			return $this->render('_view_my_contract', [
				'model' => $model,
				'print' => $print
			]);
		}

		return $this->renderAjax('_view_my_contract', [
			'model' => $model,
			'print' => $print
		]);
	}

	/**
	 * Finds the UserContracts model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id شناسه
	 *
	 * @return UserContracts the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = UserContracts::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModelContractTemplate($id)
	{
		if (($model = ContractTemplates::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	/**
	 * @throws NotFoundHttpException
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
	protected function findUserContract($contractId)
	{
		if (($model = UserContracts::find()->andWhere(['id' => $contractId, 'user_id' => Yii::$app->user->id])->one()) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	public function flash($type, $message)
	{
		Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
	}
}
