<?php

use hesabro\hris\models\EmployeeBranch;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\EmployeeBranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Employee Branch');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-branch-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::button(Module::t('module', 'Hiring'), [
                    'class' => 'btn btn-success text-white',
                    'data-size' => 'modal-lg',
                    'data-title' => Module::t('module', 'Hiring'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['employee-branch-user/create']),
                    'data-reload-pjax-container' => 'pjax-employee-branch',
                ]); ?>
                <?= Html::a(Module::t('module', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
                <?= Html::a(Module::t('module', 'Employee Branch User'), ['users'], ['class' => 'btn btn-info']) ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'id' => 'pjax-employee-branch'
                ],
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_index', [
                            'model' => $model,
                        ]);
                    },
                ],
                'title',
                [
                    'attribute' => 'manager',
                    'value' => function (EmployeeBranch $model) {
                        return $model->byManager->linkEmployee;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'user_ids',
                    'value' => function (EmployeeBranch $model) {
                        return $model->showUsersList();
                    },
                    'format' => 'raw',
                ],

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{delete}{update}{logs}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return $model->canUpdate() ? Html::a('<span class="far fa-edit text-success"></span>', ['update', 'id' => $key], [
                                'title' => Module::t('module', 'Update'),
                            ]) : '';
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a('<span class="far fa-trash-alt text-danger"></span>', ['delete', 'id' => $key], [
                                'title' => Module::t('module', 'Delete'),
                                'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                            ]) : '';
                        },
                        'logs' => function ($url, $model, $key) {
                            return Html::a('<span class="fas fa-history text-info"></span>',
                                ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => EmployeeBranch::OLD_CLASS_NAME],
                                [
                                    'class' => 'text-secondary showModalButton',
                                    'title' => Module::t('module', 'Logs'),
                                    'data-size' => 'modal-xl'
                                ]
                            );
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
