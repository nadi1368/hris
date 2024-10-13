<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRequestSearch;
use hesabro\hris\models\Letter;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class EmployeeRequestController extends Controller
{
    use AjaxValidationTrait;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['delete', 'undo'],
                        'allow' => true,
                        'verbs' => ['POST']
                    ],
                    [
                        'actions' => ['index', 'confirm', 'reject'],
                        'allow' => true,
                        'roles' => ['EmployeeRequest/admin'],
                    ],
                    [
                        'actions' => ['my', 'create', 'update'],
                        'allow' => true,
                        'roles' => ['EmployeeRequest/create'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['EmployeeRequest/view'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['EmployeeRequest/delete'],
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $searchModel = new EmployeeRequestSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionView(mixed $id, $print = false, $preview = false)
    {
        $print = (boolean) ((int) $print);
        $preview = (boolean) ((int) $preview);
        $this->layout = $print ? 'print' : ($preview ? 'base' : 'panel');
        $employeeRequest = $this->findModel($id);
        $content = $employeeRequest->indicator?->file_text;

        if ($preview && Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('Letter');
            $variables = $postData['variables'] ?? [];
            $letter = new Letter([
                'variables' => $variables,
                'employeeRequest' => $employeeRequest
            ]);

            $content = $this->renderFile('@hesabro/hris/views/employee-request/letter/template.php', [
                'letter' => $letter
            ]);
        }


        $renderMethod = $print || $preview ? 'render' : 'renderAjax';
        return $this->$renderMethod($this->viewByType('view', $employeeRequest->type), [
            'employeeRequest' => $employeeRequest,
            'print' => $print,
            'content' => $content
        ]);
    }

    public function actionMy(mixed $type)
    {
        /** @var object $user */
        $user = Yii::$app->user->getIdentity();
        $employeeBranchUser = $user->employeeBranchUser;
        $this->layout = 'panel';
        $searchModel = new EmployeeRequestSearch();

        $dataProvider = $searchModel->searchUser([
            'type' => EmployeeRequest::TYPE_LETTER,
            'branch_id' => $employeeBranchUser?->branch_id
        ], $employeeBranchUser);

        return $this->render($this->viewByType('my', $type), [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate(mixed $type)
    {
        /** @var object $user */
        $user = Yii::$app->user->getIdentity();
        $request = Yii::$app->request;
        $model = new EmployeeRequest([
            'scenario' => EmployeeRequest::SCENARIO_CREATE_OFFICIAL_LETTER,
            'type' => $type,
            'status' => EmployeeRequest::STATUS_PENDING,
            'user_id' => $user?->getId(),
            'branch_id' => $user?->employeeBranchUser?->branch_id
        ]);

        if ($request->isPost && $model->load($request->post()) && $model->validate()) {

            $save = $model->save(false);

            return $this->asJson([
                'success' => $save,
                'msg' => Module::t('module', $save ? 'Item Created' : 'Error In Save Info')
            ]);
        }

        $this->performAjaxValidation($model);

        return $this->renderAjax($this->viewByType('create', $type), [
            'model' => $model
        ]);
    }

    public function actionUpdate(mixed $id)
    {
        $model = $this->findModel($id);
        $model->scenario = EmployeeRequest::SCENARIO_UPDATE_OFFICIAL_LETTER;
        $request = Yii::$app->request;

        if ($request->isPost && $model->load($request->post()) && $model->validate()) {

            $save = $model->save(false);

            return $this->asJson([
                'success' => $save,
                'msg' => Module::t('module', $save ? 'Item updated' : 'Error In Save Info')
            ]);
        }

        return $this->renderAjax($this->viewByType('update', $model->type), [
            'model' => $model
        ]);
    }

    public function actionDelete(mixed $id)
    {
        $model = $this->findModel($id);

        $delete = !!$model->softDelete();

        return $this->asJson([
            'success' => $delete,
            'msg' => Module::t('module', $delete ? 'Item deleted' : 'Error In Save Info')
        ]);
    }

    public function actionConfirm(mixed $id)
    {
        $employeeRequest = $this->findModel($id);
        $relatedModel = match ($employeeRequest->type) {
            EmployeeRequest::TYPE_LETTER => new Letter([
                'scenario' => Letter::SCENARIO_CONFIRM,
                'employeeRequest' => $employeeRequest
            ])
        };

        if (Yii::$app->request->isPost && $relatedModel->load(Yii::$app->request->post()) && $relatedModel->validate()) {
            $confirm = $relatedModel->confirm();
            return $this->asJson([
                'success' => $confirm,
                'msg' => Module::t('module', $confirm ? 'Item Confirmed' : 'Error In Save Info')
            ]);
        }

        $this->performAjaxValidation($relatedModel);
        return $this->renderAjax($this->viewByType('confirm', $employeeRequest->type), [
            'relatedModel' => $relatedModel
        ]);
    }

    public function actionReject(mixed $id)
    {
        $employeeRequest = $this->findModel($id);
        $relatedModel = match ($employeeRequest->type) {
            EmployeeRequest::TYPE_LETTER => new Letter([
                'scenario' => Letter::SCENARIO_REJECT,
                'employeeRequest' => $employeeRequest
            ])
        };

        if (Yii::$app->request->isPost && $relatedModel->load(Yii::$app->request->post()) && $relatedModel->validate()) {
            $reject = $relatedModel->reject();
            return $this->asJson([
                'success' => $reject,
                'msg' => Module::t('module', $reject ? 'Item Rejected' : 'Error In Save Info')
            ]);
        }

        $this->performAjaxValidation($relatedModel);
        return $this->renderAjax($this->viewByType('reject', $employeeRequest->type), [
            'relatedModel' => $relatedModel
        ]);
    }

    public function actionUndo(mixed $id)
    {
        $employeeRequest = $this->findModel($id);

        $relatedModel = match ($employeeRequest->type) {
            EmployeeRequest::TYPE_LETTER => new Letter([
                'employeeRequest' => $employeeRequest
            ])
        };

        $undo = $relatedModel->undo();

        return $this->asJson([
            'success' => $undo,
            'msg' => Module::t('module', $undo ? 'Item Undid' : 'Error In Save Info')
        ]);
    }

    private function findModel($id)
    {
        if ($model = EmployeeRequest::findOne($id)) {
            return $model;
        }

        throw throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    private function viewByType(string $view, string $type)
    {
        $views = [
            EmployeeRequest::TYPE_LETTER => [
                'create' => 'letter/form',
                'update' => 'letter/form',
                'my' => 'letter/my',
                'confirm' => 'letter/confirm',
                'reject' => 'letter/reject',
                'view' => 'letter/view'
            ]
        ];

        if ($viewFile = $views[$type][$view] ?? null) {
            return $viewFile;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

}