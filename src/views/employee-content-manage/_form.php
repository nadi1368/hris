<?php

use backend\modules\master\models\Client;
use common\components\DynamicFormWidget;
use common\models\Faq;
use common\models\Tags;
use common\models\User;
use common\widgets\CKEditorWidget;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;

/** @var yii\web\View $this */
/** @var common\models\Faq $model */
/** @var yii\bootstrap4\ActiveForm $form */
/* @var string|null $type */
/* @var bool $isTypeSet */

$initValueText = '';
if ($model->custom_job_tags && ($tags = Tags::find()->andWhere(['IN', 'id', $model->custom_job_tags])->all())) {
	$initValueText = ArrayHelper::map($tags, "id", "title");
} else {
	$model->custom_job_tags = [];
}

?>

<div class="faq-form">

	<?php $form = ActiveForm::begin([
		'id' => 'form-faq',
		'options' => [
            'data-pjax' => true
        ],
	]); ?>
	<div class="card-body">
		<div class="row">

			<?php if ($isTypeSet) : ?>
				<?= $form->field($model, 'type')->hiddenInput(['value' => $type])->label(false) ?>
			<?php else : ?>
				<div class="col-md-4">
					<?= $form->field($model, 'type')->dropdownList(Faq::itemAlias('Type'), ['prompt' => Module::t('module', 'Select...')]) ?>
				</div>
			<?php endif; ?>

			<div class="<?= $isTypeSet ? 'col-md-12' : 'col-md-8' ?>">
				<?= $form->field($model, 'title')->textInput() ?>
			</div>

			<div class="col-md-12">
				<?php DynamicFormWidget::begin([
					'widgetContainer' => 'dynamicform_wrapper',
					'widgetBody' => '.clauses-container',
					'widgetItem' => '.clause-item',
					'limit' => 4,
					'min' => 1,
					'insertButton' => '.add-clause',
					'deleteButton' => '.remove-clause',
					'model' => $model->clauses[0],
					'formId' => 'form-faq',
					'formFields' => ['content'],
				]); ?>
				<div class="clauses-container">
					<?php if (!empty($model->clauses)) : ?>
						<?php foreach ($model->clauses as $index => $clause) : ?>
							<div class="clause-item form-group" id="clause-item-<?= $index ?>">
								<?= $form->field($clause, "[{$index}]id")->hiddenInput()->label(false) ?>
								<?= $form->field($clause, "[{$index}]content")->widget(CKEditorWidget::class, [
									'options' => ['rows' => 3],
									'clientOptions' => [
										'language' => 'fa',
										'removePlugins' => $model->isNewRecord ? 'image' : '',
										'filebrowserUploadUrl' => !$model->isNewRecord ? Url::to(['upload-image', 'id' => $model->id]) : false,
									]
								])->label('بندها <br/><small>کل شرح را بصورت یکجا وارد نمایید و در صورتیکه قصد نمایش بندهای مختلف را به صورت ارجاع دارید، بند مورد نظر خود را مجزا وارد کنید.</small>')
								?>

								<button type="button" class="btn btn-sm btn-danger remove-clause" data-index="<?= $index ?>">
									<?= Module::t('module', 'Remove Clause') ?>
								</button>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<button type="button" class="add-clause btn btn-dark btn-sm mb-4"><?= Module::t('module', 'Add Clause') ?></button>
				<?php DynamicFormWidget::end(); ?>
			</div>

			<?php if (Yii::$app->client->isMaster()) : ?>
				<div class="col-md-6">
					<?= $form->field($model, 'include_client_ids')->widget(Select2::class, [
						'data' => Client::itemAlias('List'),
						'options' => [
							'placeholder' => 'کسب‌وکارهایی که منحصرا قادر به مشاهده این مورد می‌باشند',
							'dir' => 'rtl',
							'multiple' => true,
						],
					])->hint('در صورت خالی بودن لیست، به تمامی کسب‌وکارها نمایش داده می‌شود') ?>
				</div>

				<div class="col-md-6">
					<?= $form->field($model, 'exclude_client_ids')->widget(Select2::class, [
						'data' => Client::itemAlias('List'),
						'options' => [
							'placeholder' => 'کسب‌وکارهایی که قادر به مشاهده این مورد نمی‌باشند.',
							'dir' => 'rtl',
							'multiple' => true,
						],
					]) ?>
				</div>
			<?php endif; ?>

            <div class="col-md-6">
                <?= $form->field($model, 'custom_job_tags')->widget(Select2::className(), [
                    'initValueText' => $initValueText,
                    'options' => [
                        'placeholder' => Yii::t("app", "Search"),
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return '" . Module::t('module', 'Select2')['Error Loading'] . "'; }"),
                            'inputTooShort' => new JsExpression("function () { return '" . Module::t('module', 'Select2')['Input Too Short'] . "'; }"),
                            'loadingMore' => new JsExpression("function () { return '" . Module::t('module', 'Select2')['Loading More'] . "'; }"),
                            'noResults' => new JsExpression("function () { return '" . Module::t('module', 'Select2')['No Results'] . "'; }"),
                            'searching' => new JsExpression("function () { return '" . Module::t('module', 'Select2')['Searching'] . "'; }"),
                            'maximumSelected' => new JsExpression("function () { return '" . Module::t('module', 'Select2')['Maximum Selected'] . "'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/tags/find', 'category' => Tags::MODEL_JOBS]),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                    ],
                ]); ?>
            </div>

            <div class="col-md-6">
				<?= $form->field($model, 'custom_user_ids')->widget(Select2::className(), [
					'data' => User::getUserWithRoles(['user']),
					'options' => [
						'placeholder' => 'کاربرانی که مجاز به مشاهده هستند',
						'dir' => 'rtl',
						'multiple' => true
					],
				]); ?>
			</div>

                <div class="col-12 col-md-6 date-input">
                    <?= $form->field($model, 'show_start_at')->widget(MaskedInput::class, [
                        'mask' => '9999/99/99',
                        'options' => [
                            'autocomplete' => 'off',
                            'value' => $model->show_start_at > 0 ? Yii::$app->jdate->date('Y/m/d', $model->show_start_at) : ''
                        ]
                    ]) ?>
                </div>
                <div class="col-12 col-md-6 date-input">
                    <?= $form->field($model, 'show_end_at')->widget(MaskedInput::class, [
                        'mask' => '9999/99/99',
                        'options' => [
                            'autocomplete' => 'off',
                            'value' => $model->show_end_at > 0 ? Yii::$app->jdate->date('Y/m/d', $model->show_end_at) : ''
                        ]
                    ]) ?>
                </div>

            <div class="col-12">
                <?= $form->field($model, 'attachment')->fileInput() ?>
            </div>

		</div>
	</div>
	<div class="card-footer">
		<?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php

$script = <<<JS

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    var ckeditorId = $(item).find("textarea").attr("id");
    CKEDITOR.replace(ckeditorId, { language: "fa" });
    updateRemoveButtons();
});

(function updateRemoveButtons() {
    var items = $(".clause-item");
    items.each(function(index) {
        if (index === 0) {
            $(this).find(".remove-clause").hide();
        } else {
            $(this).find(".remove-clause").show();
        }
    });
})();

// Handle choose and set only one of #faq-custom_job_tags and #faq-custom_user_ids fields
var jobTagsField = $('#faq-custom_job_tags');
var userIdsField = $('#faq-custom_user_ids');

function resetField (field) {
	field.val([]);
	field.trigger('change.select2');
}

function disableField(field) {
	field.prop('disabled', true);
}

function enableField(field) {
	field.prop('disabled', false);
}

function getFieldValue(field) {
	return field.val();
}

function fieldContainsValue (field) {
	return getFieldValue(field) && getFieldValue(field).length > 0;
}

function init() {
	if(fieldContainsValue(jobTagsField)) {
		resetField(userIdsField)
		disableField(userIdsField)
	}

	if(fieldContainsValue(userIdsField)) {
		resetField(jobTagsField)
		disableField(jobTagsField)
	}
}

init();

jobTagsField.on('change', function() {
	var value = $(this).val();
	if(value && value.length > 0) {
		resetField(userIdsField)
		disableField(userIdsField)
	} else {
		resetField(userIdsField)
		enableField(userIdsField)
	}
})

userIdsField.on('change', function() {
	var value = $(this).val();
	if(value && value.length > 0) {
		resetField(jobTagsField)
		disableField(jobTagsField)
	} else {
		resetField(jobTagsField)
		enableField(jobTagsField)
	}
})

JS;

$this->registerJs($script); ?>