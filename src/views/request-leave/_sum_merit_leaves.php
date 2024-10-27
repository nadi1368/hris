<?php

use hesabro\hris\models\RequestLeave;
use hesabro\hris\Module;
use yii\widgets\Pjax;

/* @var $model RequestLeave */


Pjax::begin([
    'timeout'         => false,
    'enablePushState' => false,
]) ?>
    <div class="table-responsive">
        <table class="table text-center">
            <thead class="bg-info text-white">
            <tr>
                <th> <?= Module::t('module', 'Username') ?> </th>
                <th> <?= Module::t('module', 'Sum  Merit leave in this month') ?> </th>
                <th> <?= Module::t('module', 'Sum  Merit leave in this Year') ?> </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td> <?= $model->user->fullName; ?> </td>
                <td> <?= Yii::$app->formatter->asDuration($model->sumMeritLeaves($model->user->id)['current_month'],'  و '); ?> </td>
                <td> <?= Yii::$app->formatter->asDuration($model->sumMeritLeaves($model->user->id)['current_year'],'  و '); ?> </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php

Pjax::end();