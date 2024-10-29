<?php

use hesabro\hris\models\Comfort;
use hesabro\hris\Module;
use yii\helpers\Url;

/* @var Comfort $model */
/* @var array|null $queryParams */
$bg = Comfort::itemAlias('CatBg', $model->type);
$color = Comfort::itemAlias('CatColor', $model->type);
?>
<a
        href="javascript:void(0)"
        id="create-payment-period"
        data-toggle="modal"
        data-target="#modal-pjax"
        data-size="modal-lg"
        data-title="<?= Module::t('module', 'Request') ?>"
        data-url="<?= Url::to(['create', 'comfort_id' => $model->id]) ?>"
        data-reload-pjax-container-on-show="0"
        data-reload-pjax-container="p-jax-comfort"
        data-handleFormSubmit="1"
        disabled
>
    <div class="card comfort-card h-100" style="background: linear-gradient(to left bottom, <?= $bg[0] ?>, <?= $bg[1] ?>); color: <?= $color ?>">
        <?php if ($productLogo = Module::getInstance()->productLogo): ?>
            <img src="<?= Url::to($productLogo) ?>" class="comfort-card__bg" alt="product-logo" />
        <?php endif; ?>
        <div class="card-body d-flex flex-column justify-content-between gap-2">
            <div class="d-flex align-items-center justify-content-start gap-2">
                <i class="far fa-gift comfort-card__icon"></i>
                <h5 class="card-title text-center mb-0"><?= $model->title ?></h5>
            </div>
            <div class="text-center">
                <h3 class="mb-0"><?= $model->amount_limit ? number_format((float)$model->amount_limit) . ' ' . Module::t('module', 'Rial') : Module::t('module', 'No Limit'); ?></h3>
            </div>
            <div class="d-flex align-items-end justify-content-between">
                <div class="comfort-card__rules">
                    <?php if ($model->count_limit): ?>
                        <div class="d-flex justify-content-start gap-1" data-toggle="tooltip" data-placement="top" title="تعداد مجاز درخواست برای این مورد <?= $model->count_limit ?> عدد <?= Comfort::itemAlias('TypeLimit', $model->type_limit) ?> می‌باشد.">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 256 256"><path fill="currentColor" d="M208 32h-24v-8a8 8 0 0 0-16 0v8H88v-8a8 8 0 0 0-16 0v8H48a16 16 0 0 0-16 16v160a16 16 0 0 0 16 16h160a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16M72 48v8a8 8 0 0 0 16 0v-8h80v8a8 8 0 0 0 16 0v-8h24v32H48V48Zm136 160H48V96h160zm-96-88v64a8 8 0 0 1-16 0v-51.06l-4.42 2.22a8 8 0 0 1-7.16-14.32l16-8A8 8 0 0 1 112 120m59.16 30.45L152 176h16a8 8 0 0 1 0 16h-32a8 8 0 0 1-6.4-12.8l28.78-38.37a8 8 0 1 0-13.31-8.83a8 8 0 1 1-13.85-8A24 24 0 0 1 176 136a23.76 23.76 0 0 1-4.84 14.45"/></svg>
                            <span class="comfort-card__rule-title"><?= $model->count_limit ?> <?= Module::t('module', 'Quantity') ?> <?= Comfort::itemAlias('TypeLimit', $model->type_limit) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($model->experience_limit): ?>
                        <div class="d-flex justify-content-start gap-1" data-toggle="tooltip" data-placement="top" title="برای درخواست این مورد حداقل <?= $model->experience_limit ?> ماه سابقه کار نیاز است.">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 256 256"><g fill="currentColor"><path d="M224 118.31V200a8 8 0 0 1-8 8H40a8 8 0 0 1-8-8v-81.69A191.14 191.14 0 0 0 128 144a191.08 191.08 0 0 0 96-25.69" opacity=".2"/><path d="M104 112a8 8 0 0 1 8-8h32a8 8 0 0 1 0 16h-32a8 8 0 0 1-8-8m128-40v128a16 16 0 0 1-16 16H40a16 16 0 0 1-16-16V72a16 16 0 0 1 16-16h40v-8a24 24 0 0 1 24-24h48a24 24 0 0 1 24 24v8h40a16 16 0 0 1 16 16M96 56h64v-8a8 8 0 0 0-8-8h-48a8 8 0 0 0-8 8ZM40 72v41.62A184.07 184.07 0 0 0 128 136a184 184 0 0 0 88-22.39V72Zm176 128v-68.37A200.25 200.25 0 0 1 128 152a200.19 200.19 0 0 1-88-20.36V200z"/></g></svg>
                            <span class="comfort-card__rule-title"><?= $model->experience_limit ?> <?= Module::t('module', 'Month') ?> <?= Module::t('module', 'Work Experience') ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($model->request_again_limit): ?>
                        <div class="d-flex justify-content-start gap-1" data-toggle="tooltip" data-placement="top" title="برای درخواست مجدد این مورد حداقل باید <?= $model->request_again_limit ?> روز از آخرین درخواست گذشته باشد.">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 256 256"><g fill="currentColor"><path d="M224 64v64a64 64 0 0 1-64 64H32v-64a64 64 0 0 1 64-64Z" opacity=".2"/><path d="M24 128a72.08 72.08 0 0 1 72-72h108.69l-10.35-10.34a8 8 0 0 1 11.32-11.32l24 24a8 8 0 0 1 0 11.32l-24 24a8 8 0 0 1-11.32-11.32L204.69 72H96a56.06 56.06 0 0 0-56 56a8 8 0 0 1-16 0m200-8a8 8 0 0 0-8 8a56.06 56.06 0 0 1-56 56H51.31l10.35-10.34a8 8 0 0 0-11.32-11.32l-24 24a8 8 0 0 0 0 11.32l24 24a8 8 0 0 0 11.32-11.32L51.31 200H160a72.08 72.08 0 0 0 72-72a8 8 0 0 0-8-8"/></g></svg>
                            <span class="comfort-card__rule-title"><?= $model->request_again_limit ?> <?= Module::t('module', 'Day') ?> <?= Module::t('module', 'Gap') ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($model->married): ?>
                        <div class="d-flex justify-content-start gap-1" data-toggle="tooltip" data-placement="top" title="این مورد فقط برای متاهلین قابل درخواست است.">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 256 256"><g fill="currentColor"><path d="M136 108a52 52 0 1 1-52-52a52 52 0 0 1 52 52" opacity=".2"/><path d="M117.25 157.92a60 60 0 1 0-66.5 0a95.83 95.83 0 0 0-47.22 37.71a8 8 0 1 0 13.4 8.74a80 80 0 0 1 134.14 0a8 8 0 0 0 13.4-8.74a95.83 95.83 0 0 0-47.22-37.71M40 108a44 44 0 1 1 44 44a44.05 44.05 0 0 1-44-44m210.14 98.7a8 8 0 0 1-11.07-2.33A79.83 79.83 0 0 0 172 168a8 8 0 0 1 0-16a44 44 0 1 0-16.34-84.87a8 8 0 1 1-5.94-14.85a60 60 0 0 1 55.53 105.64a95.83 95.83 0 0 1 47.22 37.71a8 8 0 0 1-2.33 11.07"/></g></svg>
                            <span class="comfort-card__rule-title"><?= Module::t('module', 'For Married') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="comfort-card__expire-date">
                    <span class="comfort-card__spec-title"><?= Module::t('module', 'Expire') ?></span>
                    <span><?= $model->expire_time ? Yii::$app->jdf::jdate("Y/m/d", $model->expire_time) : Module::t('module', 'Do Not Have'); ?></span>
                </div>
            </div>
        </div>
    </div>
</a>