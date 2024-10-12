<?php
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\assets\ProfileUpdateAssets;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var EmployeeBranchUser $model
 * @var bool $isAdmin
 * @var View $this
 */

$this->registerAssetBundle(ProfileUpdateAssets::class, View::POS_END);

$isAdmin = $model->scenario === EmployeeBranchUser::SCENARIO_INSURANCE;

$this->title = Module::t('module', 'Edit') . ' ' . Module::t('module', 'Profile');
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin(['id' => 'ajax-form-employee-update-profile', 'options' => ['enctype' => 'multipart/form-data']]);
$this->render('update/_information', compact('model', 'form', 'isAdmin'));
$this->render('update/_documents', compact('model', 'form', 'isAdmin'));
$rejectForm = $isAdmin && $model->pending_data ? $this->renderFile('@backend/modules/employee/views/default/_reject-update.php', ['model' => $model]) : null;
$rejectForm = $rejectForm ? trim(preg_replace('/\s\s+/', ' ', preg_replace("/(\/[^>]*>)([^<]*)(<)/","\\1\\3", preg_replace("/[\r\n]*/","",$rejectForm)))) : $rejectForm;
?>

<div class="card" style="background-color: transparent">
    <?= Tabs::Widget([
        'items' => [
            [
                'label' => Module::t('module', 'User Info'),
                'content' => $this->blocks['information'] ?? '',
                'active' => true
            ],
            [
                'label' => Module::t('module', 'Identity Documents'),
                'content' => $this->blocks['documents'] ?? '',
                'active' => false
            ]
        ]
    ]) ?>

    <div class="card-footer" style="background-color: #f7f7f7">
        <?= Html::submitButton(Module::t('module', $isAdmin && $model->pending_data ? 'Accept And Update Information' : 'Update'), [
            'class' => 'btn' . ($isAdmin && $model->pending_data ? ' btn-success' : ' btn-primary')
        ]) ?>
        <?php if ($rejectForm): ?>
            <button type="button" class="btn btn-danger" data-toggle="popover" title="<?= Module::t('module', 'Reject Update') ?>" data-html="true" data-placement="top" data-content="<?= htmlspecialchars($rejectForm)?>">
                <?= Module::t('module', 'Reject Update') ?>
            </button>
        <?php endif; ?>
    </div>
</div>

<?php
ActiveForm::end();
$marital = EmployeeBranchUser::MARITAL_MARRIED;
$man = Module::getInstance()->user::SEX_MAN;
$seenRejectUrl = Url::to(['profile/seen-reject']);
$js = <<<JS
window.seenRejectUrl = '$seenRejectUrl';
window.manType = parseInt('$man');
window.maritalType = parseInt('$marital');
JS;

$this->registerJs($js, View::POS_HEAD, 'profile');