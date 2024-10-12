<?php

use hesabro\hris\models\InternalNumber;
use hesabro\hris\Module;
use hesabro\hris\widgets\SortableGridView as GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\InternalNumberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Internal Numbers');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'internal-number-p-jax']); ?>
<div class="internal-number-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(
                    Module::t('module', 'Create'),
                    "javascript:void(0)",
                    [
                        'id' => 'create-internal-number',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'title' => Module::t('module', 'Create'),
                        'data-title' => Module::t('module', 'Create'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container' => "internal-number-p-jax",
                    ]
                ) ?>
                <?= Html::a(Module::t('module', 'Import Json'), 'javascript:void(0)', [
                    'class' => 'btn btn-info',
                    'data-size' => 'modal-md',
                    'data-title' => Module::t('module', 'Import Json'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['json-import']),
                    'data-reload-pjax-container' => 'internal-number-p-jax',
                    'disabled' => true
                ]); ?>

                <?= Html::a(Module::t('module', 'Export Json All'), Url::to(['json-export-all']), [
                    'class' => 'btn btn-info grid-btn grid-btn-update',
                    'data-pjax' => 0
                ]); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}{toolbar}\n<div class='table-responsive mb-2'>{items}</div>{pager}",
            'options' => ['id' => 'internal-number-grid-view', 'class' => 'grid-view'],
            // 'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'sort',
                    'label' => Module::t('module', 'Sort'),
                    'headerOptions' => ['style' => 'width:100px; text-align: right;'],
                    'format' => 'raw',
                    'value' => function () {
                        return '<div class="sortable-handle" style="cursor: move; text-align:center;"><i class="fas fa-arrows-alt"></i></div>';
                    },
                ],
                [
                    'label' => 'ردیف نمایش',
                    'value' => function ($model) {
                        return $model->sort;
                    },
                ],
                'name',
                'job_position',
                'number',
                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        return $model->user?->fullName;
                    },
                ],

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{update} {export} {delete}',
                    'buttons' => [
                        'update' => function ($url, InternalNumber $model, $key) {
                            return $model->canUpdate() ? Html::a(
                                '<i class="text-success far fa-edit"></i>',
                                ['internal-number/update', 'id' => $model->id],
                                [
                                    'title' => Module::t('module', 'Update'),
                                    'class' => 'grid-btn grid-btn-update',
                                    'id' => 'update-organization-member',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'internal-number-p-jax',
                                ]
                            ) : '';
                        },
                        'export' => function ($url, $model, $key) {
                            return Html::a(
                                '<i class="text-primary far fa-file-export"></i>',
                                Url::to(['json-export', 'id' => $model->id]),
                                [
                                    'class' => 'grid-btn grid-btn-update',
                                    'data-pjax' => 0
                                ],
                            );
                        },
                        'delete' => function ($url, $model, $key) use ($searchModel) {
                            return $model->canDelete(false) ? Html::a('<span class="fal fa-trash"></span>', ['internal-number/delete', 'id' => $model->id], [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'text-danger',
                                'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                            ]) : '';
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>