<?php

use hesabro\hris\models\Comfort;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var ComfortItems $model */
/* @var $form yii\widgets\ActiveForm */
/* @var Comfort $comfort */

$styles = <<<CSS
    .clamped {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 5;
    }
CSS;

$this->registerCss($styles);
?>

<div class="comfort-items-form">

    <?php $form = $comfort->showcase ? null : ActiveForm::begin(['id' => 'comfort-item-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="card-body">
        <div class="row">
            <?php if ($comfort->description || $comfort->getRelatedFaq() || $comfort->getRelatedFaqClause()) : ?>
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        <?= $comfort->description ?>
                        <?php if ($comfort->getRelatedFaq() || $comfort->getRelatedFaqClause()) : ?>
                            <div class="clamped"><?= ($comfort->getRelatedFaqClause())?->content ?? $comfort->getRelatedFaq()?->description ?></div>
                            <?= Html::a(Module::t('module', 'Read More'), Url::to([
                                'employee-content/index', 'type' => 2,
                                'faq_id' => $comfort->getRelatedFaq()?->id,
                                'clause_id' => $comfort->getRelatedFaqClause()?->id,
                            ]), ['target' => '_blank', 'class' => 'text-info d-inline-block']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($form): ?>
            <div class="col-12 <?= $model->scenario === ComfortItems::SCENARIO_LOAN_CREATE ? 'col-md-6' : 'col-md-12' ?>">
                <?= $form->field($model, "amount")
                    ->widget(
                        MaskedInput::class,
                        [
                            'options' => [
                                'autocomplete' => 'off',
                                'value' => $model->scenario === ComfortItems::SCENARIO_LOAN_CREATE ? $comfort->amount_limit > 0 ? $comfort->amount_limit : '' : $model->amount
                            ],
                            'clientOptions' => [
                                'alias' => 'integer',
                                'groupSeparator' => ',',
                                'autoGroup' => true,
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ]
                    ) ?>
            </div>

            <?php if ($model->scenario === ComfortItems::SCENARIO_LOAN_CREATE) : ?>
                <div class="col-12 col-md-6">
                    <?= $form->field($model, 'loan_installment')->dropdownList(ComfortItems::itemAlias('Installments')) ?>
                </div>
            <?php endif; ?>

            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>
            </div>

            <?php if ($comfort->document_required) : ?>
                <div class="col-md-12">
                    <?= $form->field($model, "attach")->fileInput() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($form): ?>
        <div class="card-footer">
            <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php
        ActiveForm::end();
        endif;
    ?>

</div>