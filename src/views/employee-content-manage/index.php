<?php

use hesabro\hris\models\EmployeeContent;
use hesabro\hris\Module;
use hesabro\hris\widgets\SortableGridView as GridView;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var \hesabro\hris\models\EmployeeContentSearch $searchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var string $title */
/* @var string $type */
/* @var string|null $type */
/* @var bool $isTypeSet */

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;

$css = <<< CSS
    .faq-desc {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;

        max-width: 300px;

        text-align: right;
    }
CSS;

$this->registerCss($css);

$this->registerCssFile('@web/fonts/bundle.css');
?>
<?php Pjax::begin(['id' => 'employee-content-pjax']); ?>
<div class="faq-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(
                    '<button class="btn btn-success text-white"> ' . Module::t('module', 'Create') . '</button>',
                    "javascript:void(0)",
                    [
                        'id' => 'faq-create',
                        'class' => 'grid-btn grid-btn-update',
                        'data-size' => 'modal-xl',
                        'data-title' => Module::t('module', 'Create') . " $title",
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create', 'type' => $type]),
                        'data-reload-pjax-container' => 'employee-content-pjax',
                        'disabled' => true
                    ]
                ); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php echo $this->render('_search', [
                'model' => $searchModel,
                'type' => $type,
                'isTypeSet' => $isTypeSet
            ]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}{toolbar}\n<div class='table-responsive mb-2'>{items}</div>{pager}",
            'options' => ['id' => 'faq-grid-view', 'class' => 'grid-view'],
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
                'title:ntext',
                [
                    'attribute' => 'description',
                    'format' => 'html',
                    'headerOptions' => ['style' => 'width:500px; text-align: right;'],
                    'value' => function (EmployeeContent $model) {
                        return '<div class="faq-desc">' . $model->getExcerpt() . '</div>';
                    }
                ],
                [
                    'attribute' => 'type',
                    'visible' => !$isTypeSet,
                    'value' => function (EmployeeContent $model) {
                        return EmployeeContent::itemAlias('Type', $model->type);
                    }
                ],
                [
                    'attribute' => 'created',
                    'value' => function (EmployeeContent $model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'creator_id',
                    'value' => function (EmployeeContent $model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update?->fullName . '">' . $model->creator?->fullName . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px; text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, EmployeeContent $model, $key) use ($type) {
                            $items = [
                                [
                                    'label' => Html::tag('span', ' ', ['class' => 'far fa-eye']) . ' ' . Module::t('module', 'View'),
                                    'url' => ['view', 'id' => $model->id],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'View'),
                                        'class' => 'showModalButton',
                                        'data-size' => 'modal-md',
                                    ],
                                ]
                            ];

                            if ($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                    'url' => ['update', 'id' => $model->id, 'type' => $type],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Update'),
                                        'data-title' => Module::t('module', 'Update'),
                                        'data-url' => Url::to(['update', 'id' => $model->id, 'type' => $type]),
                                        'data-pjax' => '0',
                                        'data-size' => 'modal-lg',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-reload-pjax-container-on-show' => 0,
                                        'data-reload-pjax-container' => 'employee-content-pjax',
                                    ],
                                ];
                            }

                            if ($model->canDelete()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Module::t('module', 'Delete'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                        'title' => Module::t('module', 'Delete'),
                                        'aria-label' => Module::t('module', 'Delete'),
                                        'data-reload-pjax-container' => 'employee-content-pjax',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['delete', 'id' => $model->id]),
                                        'class' => "text-danger p-jax-btn",
                                        'data-title' => Module::t('module', 'Delete'),
                                        'data-method' => 'post'
                                    ],
                                ];
                            }

                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Module::t('module', 'Actions')],
                                'encodeLabel' => false,
                                'label' => '<i class="far fa-list mr-1"></i>',
                                'options' => ['class' => 'float-right'],
                                'dropdown' => [
                                    'items' => $items,
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>