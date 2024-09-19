<?php

use backend\models\AdvanceMoney;
use common\components\Helper;
use common\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdvanceMoneySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
Pjax::begin(['id' => 'pjax-advance-money'])
?>
<div class="advance-money-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model) {
                return [
                    'class' => AdvanceMoney::itemAlias('StatusClass', $model->status)
                ];
            },
            //'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_detail', [
                            'model' => $model,
                        ]);
                    },
                ],
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        return $model->user->getLink();
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => Yii::t('app', 'Shaba Number'),
                    'value' => function (AdvanceMoney $model) {
                        if($model->iban) {
                            return Helper::formatterIBAN($model->iban) . '<br />' . Html::a('<i class="fa fa-copy"></i>', ['#'], ['onclick' => "return copyToClipboard('" . $model->iban . "')", 'title' => 'کپی', 'class' => 'text-info']);
                        }
                        $employeeUser = $model->employee;
                        return $employeeUser !== null && $employeeUser->shaba ? Helper::formatterIBAN($employeeUser->shaba) . '<br />' . Html::a('<i class="fa fa-copy"></i>', ['#'], ['onclick' => "return copyToClipboard('" . $employeeUser->shaba . "')", 'title' => 'کپی', 'class' => 'text-info']) : null;;
                    },
                    'format' => 'raw'
                ],
                'amount:currency',
                'comment',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return AdvanceMoney::itemAlias('Status', $model->status);
                    },
                    'filter' => AdvanceMoney::itemAlias('Status')
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdate->date("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdate->date("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw'
                ],

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' => '{confirm}{reject}{document}',
                    'buttons' => [
                        'confirm' => function ($url, $model, $key) {
                            return $model->canConfirm() ? Html::a('<span class="far fa-check"></span>',
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Confirm'),
                                    'id' => 'confirm-advance-money',
                                    'class' => 'text-success',
                                    'data-size' => 'modal-xxl',
                                    'data-title' => Yii::t('app', 'Confirm'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['confirm', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => "pjax-advance-money",
                                    'data-handle-form-submit' => 1,
                                    'disabled' => true,
                                ]) : '';
                        },
                        'reject' => function ($url, $model, $key) {
                            return $model->canReject() ? Html::a('<span class="fa fa-times"></span>',
                                'javascript:void(0)', [
                                    'title' => Yii::t('app', 'Reject'),
                                    'id' => 'reject-advance-money',
                                    'class' => 'text-danger',
                                    'data-size' => 'modal-xl',
                                    'data-title' => Yii::t('app', 'Reject'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['reject', 'id' => $model->id]),
                                    'data-reload-pjax-container-on-show' => 1,
                                    'data-reload-pjax-container' => "pjax-advance-money",
                                    'data-handle-form-submit' => 1,
                                    'disabled' => true,
                                ]) : '';
                        },

                        'document' => function ($url, $model, $key) {
                            return $model->status == AdvanceMoney::STATUS_CONFIRM ? Html::a('<span class="ti-write grid-btn grid-btn-update"></span>', ['/document/view', 'id' => $model->doc_id], [
                                'title' => Yii::t('app', 'Document'),
                                'class' => "showModalButton"
                            ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>
