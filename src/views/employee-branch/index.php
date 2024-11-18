<?php

use hesabro\hris\models\EmployeeBranch;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;

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
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

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
