<?php
/* @var $this \yii\web\View */

/* @var $content string */

use hesabro\hris\assets\PanelAssets;
use common\models\Settings;
use common\widgets\Alert;
use common\widgets\dateRangePicker\RangePickerAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;

RangePickerAsset::register($this);
PanelAssets::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html dir="rtl" lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2874f0">
    <meta http-equiv="content-language" content="fa"/>
    <meta name="city" content="Kerman">
    <meta name="state" content="Kerman">
    <meta name="country" content="Iran">
    <link rel="shortcut icon" href="<?= Yii::getAlias('@web') . "/img/hesabro.png"; ?>" type="image/png"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(($this->title ?: '') . ' ' . Module::t('module', 'HR')) . ' (HRIS)' ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody(); ?>
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<div id="main-wrapper">
    <?= $this->render('panel_header') ?>
    <?= $this->render('panel_sidebar') ?>
    <div class="page-wrapper">
        <?php if(isset($this->params['breadcrumbs'])): ?>
            <div class="page-breadcrumb bg-white">
                <div class="row flex-column flex-md-row">
                    <div class="col-12 col-lg-12 align-self-start align-self-md-center">
                        <nav class="mt-2">
                            <?= Breadcrumbs::widget([
                                'options' => ['class' => 'breadcrumb mb-0 justify-content-start  p-0 bg-white'],
                                'homeLink' => [
                                    'label' => '<span class="fal fa-home"></span>',
                                    'url' => \common\components\Url::home(),
                                    'encode' => false// Requested feature
                                ],
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
                                'activeItemTemplate' => "<li class=\"breadcrumb-item active\">{link}</li>\n",
                            ]) ?>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="page-content container-fluid text-left pt-5">
            <div>
                <?= Alert::widget() ?>
            </div>
            <?= $content ?>

            <?php
            Modal::begin([
                'headerOptions' => ['id' => 'modalHeader'],
                'id' => 'modal',
                //keeps from closing modal with esc key or by clicking out of the modal.
                // user must click cancel or X to close
                'clientOptions' => [],
                'options' => ['tabindex' => false]
            ]);
            echo "<div id='modalContent'></div>";
            Modal::end();
            ?>

            <?php
            Modal::begin([
                'headerOptions' => ['id' => 'modalPjaxOverHeader'],
                'id' => 'modal-pjax-over',
                'bodyOptions' => [
                    'id' => 'modalPjaxOverContent',
                    'class' => 'p-3',
                    'data' => ['show-preloader' => 0]
                ],
                'options' => ['tabindex' => false, 'style' => 'z-index:1051;']
            ]); ?>
            <div class="text-center">
                <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <?php Modal::end(); ?>

            <?php
            Modal::begin([
                'headerOptions' => ['id' => 'modalPjaxHeader'],
                'id' => 'modal-pjax',
                'bodyOptions' => [
                    'id' => 'modalPjaxContent',
                    'class' => 'p-3',
                    'data' => ['show-preloader' => 0]
                ],
                'options' => ['tabindex' => false]
            ]); ?>
            <div class="text-center">
                <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <?php Modal::end(); ?>
            <?php
            Modal::begin([
                'header' => Html::tag('a', Html::tag('i', '', ['class' => 'fal fa-times text-secondary font-18']), ['class' => 'p-1', 'data-dismiss' => 'modal']),
                'headerOptions' => ['class' => 'bg-white border-0 d-flex justify-content-end py-0'],
                'closeButton' => false,
                'id' => 'modal-auth',
                'size' => 'modal-sm',
                'bodyOptions' => [
                    'id' => 'modalAuthContent',
                    'class' => '',
                    'data' => ['show-preloader' => 0]
                ],
                'options' => ['tabindex' => false]
            ]); ?>
            <?php Pjax::begin([
                'id' => 'auth-pjax-container', 'timeout' => false, 'enablePushState' => false,
                'options' => [
                    'data' => [
                        'show-preloader' => 0
                    ]
                ]
            ]) ?>
            <?=
            Html::tag('div',
                Html::tag('div',
                    Html::tag('span', 'Loading ...', ['class' => 'sr-only']),
                    ['class' => 'spinner-grow text-mobit p-3', 'role' => 'status']),
                ['class' => 'd-flex justify-content-center align-items-center', 'style' => 'min-height:150px;']);
            ?>
            <?php Pjax::end() ?>
            <?php Modal::end(); ?>
        </div>
    </div>
</div>
<?= $this->render('@backend/views/layouts/_footer') ?>

<?php $this->endBody() ?>

<?php
$today_date=Yii::$app->jdf->jdate('Y/m/d');
$script_doday_value = <<< JS
today_date="$today_date";
JS;
$this->registerJs($script_doday_value);
?>

<div class="modal fade top-modal-with-space" id="myModal12" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content-wrap">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">بشوت به</h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control input-lg m-bot15" placeholder="سفارش,محصول,تنوع"  id="shortCode" data-base-url="<?= \common\components\Url::base() ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-pay">
            <div class="modal-header border-0 flex-row align-items-center">
                <button type="button" class="close mr-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">تائید</h4>
            </div>
            <div class="modal-body">
                <h4 class="title-confirm text-left"></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                        onclick="modalConfirm()"><?= Module::t('module', 'Yes') ?></button>
                <button type="button" class="btn btn-default" onclick="modalClose()">
                    <?= Module::t('module', 'No') ?>
                </button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php $this->endPage() ?>
