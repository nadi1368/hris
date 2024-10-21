<?php

use common\models\Faq;
use common\components\mobit\SortableGridview\SortableGridView as GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FaqSearch */
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
<?php Pjax::begin(['id' => 'faq-p-jax']); ?>
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
                        'data-reload-pjax-container' => 'faq-p-jax',
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
                    'value' => function (Faq $model) {
                        return '<div class="faq-desc">' . $model->getExcerpt() . '</div>';
                    }
                ],
                [
                    'attribute' => 'type',
                    'visible' => !$isTypeSet,
                    'value' => function (Faq $model) {
                        return Faq::itemAlias('Type', $model->type);
                    }
                ],
                [
                    'attribute' => 'created',
                    'value' => function (Faq $model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'creator_id',
                    'value' => function (Faq $model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update?->fullName . '">' . $model->creator?->fullName . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, Faq $model, $key) {
                            return Html::a(
                                '<i class="text-info far fa-eye"></i>',
                                ['view', 'id' => $model->id],
                                [
                                    'title' => Module::t('module', 'View'),
                                    'class' => 'grid-btn grid-btn-view showModalButton'
                                ]
                            );
                        },
                        'update' => function ($url, $model, $key) use ($type, $title) {
                            return $model->canUpdate() ? Html::a(
                                '<i class="text-success far fa-edit"></i>',
                                "javascript:void(0)",
                                [
                                    'id' => 'faq-update',
                                    'class' => 'grid-btn grid-btn-update',
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Update') . " $title",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $model->id, 'type' => $type]),
                                    'data-reload-pjax-container' => 'faq-p-jax',
                                    'disabled' => true,
                                ]
                            ) : '';
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(
                                '<span class="ti-trash grid-btn grid-btn-delete"></span>',
                                ['delete', 'id' => $key],
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                ]
                            ) : '';
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>