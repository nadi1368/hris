<?php
use hesabro\hris\models\EmployeeBranchUser;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var EmployeeBranchUser $model
 * @var View $this
 */

$form = ActiveForm::begin([
    'id' => 'employee-reject-update',
    'action' => ['default/reject-update', 'user_id' => $model->user_id]
]);
$model->scenario = EmployeeBranchUser::SCENARIO_REJECT_UPDATE;
?>

<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'reject_update_description')->textarea([
            'rows' => 4,
            'value' => ''
        ]) ?>
    </div>
    <div class="col-12">
        <?= Html::submitButton(Yii::t('app', 'Reject Update'), [
            'class' => 'btn btn-danger w-100'
        ]) ?>
    </div>
</div>

<?php
ActiveForm::end();

$js = <<<JS
$(document).on('submit', '#employee-reject-update', function(event) {
    event.preventDefault()
    event.stopPropagation()
    const form = $(this)
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: form.serialize(),
        success: function (response) {
            if (response) {
                $('#modal-pjax')?.modal('hide')
                showtoast(response.msg, response.success ? 'success' : 'error');
            }
        },
        error: function () {
            showtoast('خطایی رخ داده است.', 'error');
        }
    })
})
JS;

$this->registerJs($js);
?>
