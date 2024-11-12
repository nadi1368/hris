<?php

use hesabro\hris\models\EmployeeContent;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model EmployeeContent */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Contents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-view card">
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'title:ntext',
                [
                    'attribute' => 'description',
                    'format' => 'html',
                    'value' => $model->getContent(),
                ],
                [
                    'attribute' => 'type',
                    'value' => EmployeeContent::itemAlias('Type', $model->type),
                ],
                ...($model->type === EmployeeContent::TYPE_ANNOUNCEMENT ? [
                    [
                        'attribute' => 'show_start_at',
                        'value' => $model->show_end_at ? Yii::$app->jdf::jdate('Y/m/d', $model->show_start_at) : '-',
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'show_end_at',
                        'value' => $model->show_end_at ? Yii::$app->jdf::jdate('Y/m/d', $model->show_end_at) : '-',
                        'format' => 'html'
                    ],
                ] : []),
                [
                    'attribute' => 'attachment',
                    'value' => ($attachment = $model->getFileUrl('attachment')) ?
                        Html::a(Module::t('module', 'Download'), $attachment, ['target' => '_blank', 'download' => true]) :
                        '-',
                    'format' => 'raw'
                ],
            ],
        ]) ?>
    </div>
</div>