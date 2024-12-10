<?php

use hesabro\helpers\widgets\grid\GridView;
use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap4\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\AdvanceMoneySearch */
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
                        return $this->render('_detail', [
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
                    'label' => Module::t('module', 'Shaba Number'),
                    'value' => function (AdvanceMoney $model) {
                        if ($model->iban) {
                            return Yii::$app->helper::formatterIBAN($model->iban) . '<br />' . Html::a('<i class="fa fa-copy"></i>', ['#'], ['onclick' => "return copyToClipboard('" . $model->iban . "')", 'title' => 'کپی', 'class' => 'text-info']);
                        }
                        $employeeUser = $model->employee;
                        return $employeeUser !== null && $employeeUser->shaba ? Yii::$app->helper::formatterIBAN($employeeUser->shaba) . '<br />' . Html::a('<i class="fa fa-copy"></i>', ['#'], ['onclick' => "return copyToClipboard('" . $employeeUser->shaba . "')", 'title' => 'کپی', 'class' => 'text-info']) : null;;
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
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->changed) . '">' . Yii::$app->jdf->jdate("Y/m/d  H:i", $model->created) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, AdvanceMoney $model, $key) {
                            $items = [];

                            if ($model->canConfirm()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-check-circle']) . ' ' . Module::t('module', 'Confirm'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Confirm'),
                                        'id' => 'confirm-advance-money',
                                        'class' => 'text-success',
                                        'data-size' => 'modal-xxl',
                                        'data-title' => Module::t('module', 'Confirm'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['confirm', 'id' => $model->id]),
                                        'data-reload-pjax-container' => "pjax-advance-money",
                                    ],
                                ];
                            }
                            if ($model->canUseFinno()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-check-circle']) . ' ' . 'حساب واسط',
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => 'تایید پرداخت',
                                        'id' => 'confirm-interface-ipg',
                                        'data-size' => 'modal-xl',
                                        'data-title' => 'تایید پرداخت',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['confirm-interface', 'id' => $model->id]),
                                        'data-reload-pjax-container-on-show' => 1,
                                        'data-reload-pjax-container' => 'pjax-advance-money',
                                    ],
                                ];
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-check-circle']) . ' ' . 'پرداخت شبا فینوتک',
                                    'url' => Url::to(['advance-money/deposit-into-account-by-finno', 'id' => $model->id]),
                                    'encode' => false,
                                    'linkOptions' => [
                                        'class' => 'showModalButton',
                                        'data-size' => 'modal-xl',
                                        'title' => 'پرداخت شبا فینوتک',
                                    ],
                                ];
                            }
                            if ($model->canTransferToMultiPay()) {

                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-sign-in-alt']) . ' ' . 'انتقال به حواله گروهی',
                                    'url' => Url::to(['transfer-to-multi-pay', 'id' => $key]),
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => 'انتقال به حواله گروهی',
                                        'data-confirm' => Yii::t('app', 'Are you sure?'),
                                        'data-method' => 'post',
                                        'class' => 'ajax-btn',
                                        'data-view' => 'index',
                                        'data-p-jax' => '#pjax-advance-money',
                                    ],
                                ];
                            }
                            if ($model->canReject()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-minus-circle']) . ' ' . Module::t('module', 'Reject'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'id' => 'reject-advance-money',
                                        'class' => 'text-danger',
                                        'data-size' => 'modal-xl',
                                        'data-title' => Module::t('module', 'Reject'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['reject', 'id' => $model->id]),
                                        'data-reload-pjax-container' => "pjax-advance-money",
                                    ],
                                ];
                            }
                            $items = $model->buttonDropdownDocument($items);
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Yii::t('app', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => AdvanceMoney::OLD_CLASS_NAME],
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Yii::t('app', 'Log'),
                                    'class' => 'showModalButton',
                                    'data-size' => 'modal-xxl',
                                ],
                            ];

                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Yii::t('app', 'Actions')],
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
//                [
//                    'class' => 'common\widgets\grid\ActionColumn',
//                    'template' => '{confirm}{reject}{document}',
//                    'buttons' => [
//
//                        'document' => function ($url, $model, $key) {
//                            return $model->status == AdvanceMoney::STATUS_CONFIRM ? Html::a('<span class="ti-write grid-btn grid-btn-update"></span>', ['/document/view', 'id' => $model->doc_id], [
//                                'title' => Module::t('module', 'Document'),
//                                'class' => "showModalButton"
//                            ]) : '';
//                        },
//                    ]
//                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end(); ?>
