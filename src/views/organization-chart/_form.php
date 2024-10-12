<?php

use hesabro\hris\models\OrganizationMember;
use hesabro\hris\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\hris\models\OrganizationMember */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['id' => 'organization-member-form']); ?>
<div class="form-row">
    <div class="col-md-4">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'user_id')->widget(Select2::class, [
            'data' => Module::getInstance()->user::getUserWithRoles(['user']),
            'options' => [
                'placeholder' => 'کاربر مرتبط',
                'dir' => 'rtl',
            ],
        ]); ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'parent_id')->widget(Select2::class, [
            'data' => ArrayHelper::map(OrganizationMember::find()->all(), 'id', 'name'),
            'options' => [
                'placeholder' => 'این عضو زیرمجموعه چه کسی می باشد؟',
                'dir' => 'rtl',
            ],
        ]); ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'headline')->textarea(['maxlength' => true, 'rows' => 2]) ?>
    </div>
</div>
<div>
    <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>