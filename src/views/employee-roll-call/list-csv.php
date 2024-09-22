<?php

use common\models\UploadExcel;
use common\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UploadExcelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'فایل های اکسل حضور و غیاب';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header text-right">
        <?= Html::a('آپلود فایل حضور و غیاب روزانه', ['employee-roll-call/upload-csv-daily'], ['class' => 'btn btn-success']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'id' => 'p-jax-list-csv'
                ]
            ],
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->status == UploadExcel::STATUS_INSERTED) {
                    return ['class' => 'success'];
                } else {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return UploadExcel::itemAlias('Status', $model->status);
                    },
                    'filter' => UploadExcel::itemAlias('Status'),
                ],
                [
                    'attribute' => 'file_name',
                    'value' => function ($model) {
                        $storageFile = $model->storageFile;
                        return Html::a($storageFile?->file_name, $storageFile?->getFileUrl(), ['class' => 'text-info', 'data-pjax' => 0]);
                    },
                    'format' => 'raw',
                ],
                //'created',
                'date',
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'creator_id',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update?->fullName . '">' . $model->creator?->fullName . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => "{delete}{log}",
                    'buttons' => [
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a(Html::tag('span', '', ['class' => "far fa-trash-alt ml-2"]), 'javascript:void(0)',
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-list-csv',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete-csv-daily', 'id' => $model->id]),
                                    'class' => " text-danger p-jax-btn",
                                    'data-title' => Yii::t('yii', 'Delete'),
                                    'data-method' => 'post'

                                ]) : '';
                        },
                        'log' => function ($url, $model, $key) {
                            return Html::a('<span class="fas fa-history text-info"></span>',
                                ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
                                [
                                    'class' => 'text-secondary showModalButton',
                                    'title' => Yii::t('app', 'Logs'),
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
