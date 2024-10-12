<?php

use hesabro\hris\models\OrganizationMember;
use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\OrganizationMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Organization Chart');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'organization-chart-p-jax']); ?>
<div class="organization-chart-index card">
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
                        'id' => 'total-pos-confirm',
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'title' => Module::t('module', 'Create'),
                        'data-title' => Module::t('module', 'Create'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container' => "organization-chart-p-jax",
                    ]
                ) ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function (OrganizationMember $model) {
                        return '<div class="d-inline-flex align-items-center gap-3">' . (
                            $model->user?->getFileUrl('avatar') ? '<img src="' . $model->user?->getFileUrl('avatar') . '" class="rounded-circle" width="36px" height="36px"/>' : '') .
                            '<strong>' . $model->name . '</strong>
						</div>';
                    },
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function (OrganizationMember $model) {
                        return $model->user->fullName;
                    },
                ],
                [
                    'attribute' => 'parent_id',
                    'value' => function (OrganizationMember $model) {
                        return $model->parent?->name;
                    },
                ],
                [
                    'attribute' => 'headline',
                    'format' => 'raw',
                    'value' => function (OrganizationMember $model) {
                        return $model->getFullHeadline();
                    },
                ],
                [
                    'attribute' => 'show_internal_number',
                    'format' => 'raw',
                    'value' => function (OrganizationMember $model) {

                        if (!$model->getInternalNumber())
                            return '<button class="btn btn-sm btn-secondary" disabled>' . Module::t('module', 'No Internal Number') . '</button>';

                        return Html::a(
                            $model->show_internal_number ? '<i class="ti-check fa-2x" ></i>' : '<i class="ti-close fa-2x" ></i>',
                            ['organization-chart/toggle-show-internal-number', 'id' => $model->id],
                            [
                                'class' => 'ajax-btn ' . ($model->show_internal_number ? 'text-success' : 'text-danger'),
                                'data-method' => 'post',
                                'title' => 'تغییر وضعیت نمایش',
                                'data-confirm' => false,
                                'data-view' => 'index',
                                'data-p-jax' => '#organization-chart-p-jax',
                            ]
                        );
                    },
                ],
                [
                    'attribute' => 'show_job_tag',
                    'format' => 'raw',
                    'value' => function (OrganizationMember $model) {

                        if (!$model->getJobTag())
                            return '<button class="btn btn-sm btn-secondary" disabled>' . Module::t('module', 'No Job Tag') . '</button>';

                        return Html::a(
                            $model->show_job_tag ? '<i class="ti-check fa-2x" ></i>' : '<i class="ti-close fa-2x" ></i>',
                            ['organization-chart/toggle-show-job-tag', 'id' => $model->id],
                            [
                                'class' => 'ajax-btn ' . ($model->show_job_tag ? 'text-success' : 'text-danger'),
                                'data-method' => 'post',
                                'title' => 'تغییر وضعیت نمایش',
                                'data-confirm' => false,
                                'data-view' => 'index',
                                'data-p-jax' => '#organization-chart-p-jax',
                            ]
                        );
                    },
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, OrganizationMember $model, $key) {
                            return $model->canUpdate() ? Html::a(
                                '<i class="text-success far fa-edit"></i>',
                                ['organization-chart/update', 'id' => $model->id],
                                [
                                    'title' => Module::t('module', 'Update'),
                                    'class' => 'grid-btn grid-btn-update',
                                    'id' => 'update-organization-member',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'organization-chart-p-jax',
                                ]
                            ) : '';
                        },
                        'delete' => function ($url, $model, $key) use ($searchModel) {
                            return $model->canDelete() ? Html::a('<span class="fal fa-trash"></span>', ['organization-chart/delete', 'id' => $model->id], [
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