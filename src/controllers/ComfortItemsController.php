<?php

namespace hesabro\hris\controllers;

use common\models\Comments;
use Exception;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\ComfortItemsSearch;
use hesabro\hris\models\RejectForm;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ComfortItemsController extends Controller
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
                    'revert' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['Comfort/view', 'superadmin'],
                            'actions' => ['index', 'view', 'view-attach', 'comments', 'refer']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['Comfort/confirm', 'superadmin'],
                            'actions' => ['confirm', 'reject', 'revert']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all ComfortItems models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComfortItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->withCommentsCount();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ComfortItems model.
     * @param int $id شناسه
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionComments($id)
    {
        $comfortItem = $this->findModel($id);

        $thread = $comfortItem->getComments()->parentOrRefer()->orderBy(['id' => SORT_DESC])->limit(1)->one();

        return $this->renderAjax('@backend/views/ticket/_thread.php', [
            'thread' => $thread
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewAttach($id)
    {
        $model = $this->findModel($id);
        return $this->renderPartial('_view-attach', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @param $status
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws Yii\base\ExitException
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(ComfortItems::SCENARIO_REJECT);
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
                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Module::getInstance()->id . '/comfort-items/reject');
                    $result = [
                        'success' => false,
                        'msg' => $e->getMessage()
                    ];
                }

                return $this->asJson($result);
            }
        } else {
            throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
        }

        $this->performAjaxValidation($form);

        return $this->renderAjax('_form_reject', [
            'model' => $form,
        ]);
    }

    public function actionRevert($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(ComfortItems::SCENARIO_REVERT);

        if ($model->canRevert()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model->status = ComfortItems::STATUS_WAIT_CONFIRM;
                if ($model->save() && $model->deleteAdvanceMoney() && $model->deleteSalaryItemAddition()) {
                    $transaction->commit();
                    return $this->asJson([
                        'success' => true,
                        'msg' => Module::t('module', 'Item Reverted')
                    ]);
                }
                $transaction->rollBack();
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Module::getInstance()->id . '/comfort-items/revert');
            }
        }

        $this->performAjaxValidation($model);
        return $this->asJson([
            'success' => false,
            'msg' => Module::t('module', 'Error In Save Information, Please Try Again')
        ]);
    }

    /**
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(ComfortItems::SCENARIO_CONFIRM);
        $comment = new Comments();
        if (!$model->canConfirm()) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = ComfortItems::STATUS_CONFIRM;
                $flag = $model->save(false);
                if($flag && $model->saveAdvanceMoney)
                {
                    $flag = $model->saveAdvanceMoney();
                }

                $flag = $flag && $model->createSalaryItemAddition();

                if ($flag && $comment->load(Yii::$app->request->post())) {
                    $flag = $model->createComment($comment);
                }

                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', "Item Confirmed")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
            }

            return $this->asJson($result);
        }

        $this->performAjaxValidation($model);
        if ($comment->hasErrors()) {
            $this->performAjaxValidation($comment);
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'comment' => $comment
        ]);
    }

    public function actionRefer($id)
    {
        $model = $this->findModel($id);
        $comment = new Comments();

        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];

        if ($comment->load(Yii::$app->request->post()) && $comment->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($model->createComment($comment)) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t('module', 'Item Referred')
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
            }

            return $this->asJson($result);
        }

        $this->performAjaxValidation($comment);
        return $this->renderAjax('_refer', [
            'model' => $model,
            'comment' => $comment
        ]);
    }

    /**
     * Finds the ComfortItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id شناسه
     * @return ComfortItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModel($id)
    {
        if (($model = ComfortItems::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    private function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
