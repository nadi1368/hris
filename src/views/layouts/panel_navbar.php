<?php

use backend\modules\managementPanel\models\mongo\MGNotification;
use common\models\Comments;
use common\models\Rbac;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $resellerOrderCount integer */
/* @var $orderCount integer */
/* @var $cancelRequestCount integer */

$identity = Yii::$app->user->identity;
?>

<!-- ============================================================== -->
<!-- Right side toggle and nav items -->
<!-- ============================================================== -->
<ul class="navbar-nav float-right">
    <?php if (!Yii::$app->user->isGuest) : ?>
        <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link dropdown-toggle " title="اعلان"
               data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                <i class="fas fa-2x pt-3 fa-bell"></i>
                <?php if (MGNotification::find()->own()->newest()->count()) : ?>
                    <div class="notify" id="notification-notify">
                        <span class="point" style="top: -14px;"></span>
                        <span class="heartbit" style="top: -24px;"></span>
                    </div>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                <div class="p-3 mb-2 border-bottom">
                    <h4 class="text-center"> اعلان ها </h4>
                </div>
                <div class="scroll-sidebar" style="height: 550px;" data-spy="scroll">
                    <?php foreach (MGNotification::find()->own()->newest()->orderBy(['time' => SORT_DESC])->all() as $index => $notification) : /** @var MGNotification $notification */ ?>
                        <div>
                            <a href="<?= Url::to(['/management-panel/notification/view', 'id' => (string)$notification->_id]) ?>"
                               onclick="$('#notification-notify').remove();">
                                <div class="border-bottom p-4">
                                    <?= $index + 1 . ' - ' . strip_tags($notification->title) ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </li>
        <!--        --><?php //if (isset(Yii::$app->controller->categorySetting)) : ?>
        <!--            <li class="nav-item">-->
        <!--                --><?php //= $this->render('_group_setting') ?>
        <!--            </li>-->
        <!--        --><?php //endif; ?>
        <!--        <li class="nav-item">-->
        <!--            --><?php //= $this->render('_guide') ?>
        <!--        </li>-->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle waves-effect waves-dark pro-pic" href=""
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fal fa-user-circle fa-2x pt-3 pull-right"></i>
                <span class="ml-2 user-text font-medium">
                                <?php $full_name = \Yii::$app->phpNewVer->trim(Yii::$app->user->identity->fullName); ?>
                                <?= !empty($full_name) ? $full_name : Yii::$app->user->identity->username ?>
                            </span>
                <span class="fal fa-angle-down ml-2 user-text"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                <div class="d-flex no-block align-items-center p-3 mb-2 border-bottom">
                    <div>
                        <i class="fal fa-user-circle fa-4x m-2"></i>
                    </div>
                    <div class="ml-2">
                        <h4 class="mb-0"><?= Yii::$app->user->identity->username ?></h4>
                        <p class=" mb-0 text-muted"><?= $full_name ?></p>
                    </div>
                </div>

                <?php
                $originalId = Yii::$app->session->get('user.idbeforeswitch');
                if ($originalId) :
                    ?>
                    <a href="<?= Url::to(['/user-main/login-back']) ?>" class="dropdown-item">
                        <i class="fal fa-reply icon-size"></i>
                        برگشت به حساب
                    </a>
                <?php endif; ?>
                <a href="<?= Url::to(['/profile/index']) ?>" class="dropdown-item">
                    <i class="fal fa-user icon-size"></i>
                    <?= Yii::t('app', 'Profile').' Pulse' ?>
                </a>
                <a href="<?= Url::to(['/employee/profile/update']) ?>" class="dropdown-item">
                    <i class="fal fa-user icon-size"></i>
                    <?= Yii::t('app', 'Edit') ?> <?= Yii::t('app', 'Profile').' Pulse' ?>
                </a>
                <a href="<?= Url::to(['/ticket/inbox']) ?>" class="dropdown-item">
                    <i class="fas fa-envelope icon-size"></i>
                    صندوق پیام
                </a>
                <a class="dropdown-item" href="<?= Url::to(['/authenticator']) ?>" data-method="post"
                   title="<?= Yii::t('app', '2-Step Verification') ?>">
                    <i class="fal fa-lock"></i>
                    <?= Yii::t('app', '2-Step Verification') ?>
                </a>

                <?php Pjax::begin(['id' => 'send-report-p-jax']); ?>

                <?= Html::a('<i class="fa fa-paper-plane"></i> ' . 'ارسال تیکت پشتیبانی', [
                    '/ticket/send',
                    'type' => Comments::TYPE_MASTER,
                ], [
                    'title' => 'تیکت پشتیبانی',
                    'class' => 'dropdown-item showModalButton'
                ]) ?>

                <?php Pjax::end(); ?>
                <?php if (Yii::$app->client->id == 1 && (Yii::$app->user->can('master') || Yii::$app->user->can('DEVELOPER'))): ?>
                    <a href="<?= Url::to(['/site/login-manager-branch']) ?>" class="dropdown-item">
                        <i class="fas fa-sign-in icon-size"></i>
                        Login as manager branch
                    </a>
                <?php endif; ?>
                <a class="dropdown-item" href="<?= Url::to(['/site/logout']) ?>" data-method="post"
                   title="<?= Yii::t('app', 'Logout') ?>">
                    <i class="fas fa-power-off"></i>
                    <?= Yii::t('app', 'Logout') ?>
                </a>
                <div>
                    <div class="p-3 border-bottom">
                        <!-- Logo BG -->
                        <h5 class="font-medium mb-2 mt-2">رنگ پس زمینه لوگو</h5>
                        <ul id="theme-logo-bg" class="theme-color">
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin1"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin2"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin3"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin4"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin5"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-logobg="skin6"></a>
                            </li>
                        </ul>
                        <!-- Logo BG -->
                    </div>
                    <div class="p-3 border-bottom">
                        <!-- Navbar BG -->
                        <h5 class="font-medium mb-2 mt-2">رنگ پس زمینه هدر</h5>
                        <ul id="theme-navbar-bg" class="theme-color">
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin1"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin2"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin3"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin4"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin5"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-navbarbg="skin6"></a>
                            </li>
                        </ul>
                        <!-- Navbar BG -->
                    </div>
                    <div class="p-3 border-bottom">
                        <!-- Logo BG -->
                        <h5 class="font-medium mb-2 mt-2">رنگ پس زمینه منو</h5>
                        <ul id="theme-sidebar-bg" class="theme-color">
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin1"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin2"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin3"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin4"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin5"></a>
                            </li>
                            <li class="theme-item">
                                <a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin6"></a>
                            </li>
                        </ul>
                        <!-- Logo BG -->
                    </div>
                </div>
            </div>

        </li>
    <?php endif; ?>
    <!-- ============================================================== -->
    <!-- User profile and search -->
    <!-- ============================================================== -->
</ul>
