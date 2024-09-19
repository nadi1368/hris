<?php
use hesabro\hris\models\Letter;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/**
 * @var Letter $relatedModel
 */
$form = ActiveForm::begin(['id' => 'form-confirm-letter']);
$inputs = $relatedModel->getVariablesInput($form);
?>

<div class="card mb-0">
    <div class="card-body d-flex align-items-center justify-content-end">
        <div class="date-input">
            <?= $form->field($relatedModel, 'date')->widget(MaskedInput::class, [
                'mask' => '9999/99/99',
                'options' => [
                    'value' => Yii::$app->jdate->date('Y/m/d'),
                    'autocomplete' => 'off',
                ]
            ]) ?>
        </div>
    </div>
    <div class="card-body">
        <div>
            <?= strtr($relatedModel->contractTemplate->description, $inputs) ?>
        </div>

        <div class="mt-5 text-justify">
            <?php foreach ($relatedModel->contractTemplate->clauses as $clause): ?>
                <?= strtr($clause['description'], $inputs) ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-5">
            <?= strtr($relatedModel->contractTemplate->signatures, $inputs) ?>
        </div>
    </div>

    <div class="card-footer d-flex align-items-center justify-content-end gap-2">
        <?= Html::button(Yii::t('app', 'Preview'), ['id' => 'letter-preview', 'class' => 'btn btn-info']) ?>
        <?= Html::submitButton(Yii::t('app', 'Confirm') . ' ' . Yii::t('app', 'Request'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php
ActiveForm::end();
$preview = Url::to(['view', 'id' => $relatedModel->employeeRequest->id, 'preview' => 1]);
$js = <<<JS
$('#form-confirm-letter input').on('input', function (e) {
    $('#form-confirm-letter').find('input').each((idx, item) => {
        if (e.target !== item && e.target.name === item.name) {
            item.value = e.target.value
        }
    })
})
$('#letter-preview').on('click', function(e) {
    e.preventDefault()
    const form = $('#form-confirm-letter')
    $.ajax({
        type: 'POST',
        url: '$preview',
        data: form.serialize(),
        success: function(data) {
            const previewWindow = window.open('', 'MsgWindow', "width=620,height=877,location=no,resizable=no,menubar=no")
            previewWindow.document.write(data)
        }
    });
})
JS;
$this->registerJs($js);
?>

