<?php

use hesabro\hris\models\EmployeeBranchUser;
use common\components\Helper;
use common\widgets\grid\GridView;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\hris\models\EmployeeBranchUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employee Branch User');
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
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search-user', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?php Pjax::begin(['id' => 'pjax-employee-user']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'branch_id',
                    'value' => function (EmployeeBranchUser $model) {
                        return $model->branch->title;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function (EmployeeBranchUser $model) {
                        $avatar = $model->user->getFileUrl('avatar');
                        $name = Html::tag('span', $model->user->linkEmployee);
                        $avatar = $avatar ?
                            Html::img($avatar, ['alt' => $model->user->linkEmployee, 'class' => 'rounded-circle ml-1', 'width' => '36', 'height' => '36']) :
                            Html::tag('i', '', ['class' => 'fal fa-user-circle ml-1 fa-3x']);

                        $content = Html::tag('div', $avatar . $name, ['class' => 'd-flex align-items-center justify-content-start gap-4']);

                        if ($model->pending_data) {
                            return Html::tag('span', $content, [
                                'data-title' => 'دارای ویرایش تایید نشده',
                                'data-toggle' => 'tooltip',
                                'class' => 'pulse-notification'
                            ]);
                        }

                        return $content;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'account_id',
                    'value' => function (EmployeeBranchUser $model) {
                        return $model->account?->getLink();
                    },
                    'format' => 'raw',
                ],
                'salary:currency',
                [
                    'attribute' => 'shaba',
                    'value' => function (EmployeeBranchUser $model) {
                        $shaba = Helper::formatterIBAN($model->shaba);

                        return $shaba ? substr($shaba, 0, 6) . '...' . substr($shaba, -12, 12) . '<br />' . Html::a('<i class="fa fa-copy"></i>', ['#'], ['onclick' => "return copyToClipboard('" . $model->shaba . "')", 'title' => 'کپی', 'class' => 'text-info']) : null;
                    },
                    'format' => 'raw',

                ],
                'first_name',
                'last_name',
                [
                    'attribute' => 'job_code',
                    'value' => function (EmployeeBranchUser $model) {
                        return $model->job_code ? $model->salaryInsurance?->fullName : null;
                    },
                    'format' => 'raw',

                ],
                'insurance_code',
                [
                    'attribute' => 'roll_call_id',
                    'label' => Yii::t('app', 'Traffic ID'),
                    'contentOptions' => ['title' => 'شناسه دستگاه حضور و غیاب'],
                    'headerOptions' => ['title' => 'شناسه دستگاه حضور و غیاب']
                ],
//				[
//					'label' => 'امتیازات',
//					'value' => function ($model) {
//						return UserPoints::countRequestPayment($model->user_id);
//					},
//					'format' => 'raw',
//				],

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => "{group}",
                    'buttons' => [
                        'group' => function ($url, EmployeeBranchUser $model, $key) {
                            $items = [];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-eye']) . ' ' . Yii::t("app", "Details"),
                                'url' => ['view-user', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id],
                                'encode' => false,
                                'linkOptions' => [
                                    'data-pjax' => '0'
                                ],
                            ];
                            if ($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Yii::t('app', 'Update'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Yii::t('app', 'Update'),
                                        'data-size' => 'modal-xl',
                                        'data-title' => Yii::t('app', 'Update') . ' - ' . $model->user->fullName,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['update-user', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                                        'data-reload-pjax-container' => "pjax-employee-user",
                                    ],
                                ];
                            }
                            if ($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Yii::t('app', 'Insurance Data'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Yii::t('app', 'Insurance Data'),
                                        'data-size' => 'modal-xxl',
                                        'data-title' => Yii::t('app', 'Insurance Data') . ' - ' . $model->user->fullName,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['insurance-data', 'branch_id' => $model->branch_id, 'user_id' => $model->user_id]),
                                        'data-action' => 'edit-ipg',
                                        'data-reload-pjax-container' => "pjax-employee-user",
                                    ],
                                ];
                            }
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-money-bill']) . ' ' . Yii::t('app', 'Advance Money'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Yii::t('app', 'Advance Money'),
                                    'data-size' => 'modal-xl',
                                    'data-title' => Yii::t('app', 'Advance Money') . ' - ' . $model->user->fullName,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['advance-money-manage/create-with-confirm', 'user_id' => $model->user_id]),
                                    'data-action' => 'edit-ipg',
                                    'data-reload-pjax-container' => "pjax-employee-user",
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', '', ['class' => 'fa fa-list']) . ' ' . 'مشاهده دروه های قبلی',
                                'url' => ['salary-period-items/user', 'id' => $model->user_id],
                                'encode' => false,
                                'linkOptions' => [
                                    'data-pjax' => '0'
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Yii::t('app', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelId' => $model->user_id, 'modelClass' => EmployeeBranchUser::class],
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
                            ]);;
                        },
                    ]
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
