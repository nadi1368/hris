<?php

use hesabro\hris\models\ContractClausesModel;
use hesabro\hris\models\ContractTemplates;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\helpers\widgets\CKEditorWidget;
use hesabro\hris\Module;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ContractTemplates */
/* @var $form yii\bootstrap4\ActiveForm */

$this->registerJsFile("@web/js/contractTemplate.js", ['depends' => [\yii\web\JqueryAsset::class]]);


$variables = '';
$counter = 1;
foreach (EmployeeBranchUser::itemAlias('insuranceDataDefaultVariables') as $variable => $item) {
	$variables .= '<span class="copy" onclick="return copyToClipboard(\'{' . $variable . '}\')">{' . $variable . '} = ' . $item . '</span> | ';
	if ($counter % 4 == 0) {
		$variables .= '<br>';
	}
	$counter++;
}

$typeTextFa = Module::t('module', $model->typeText);
?>
	<span class="copy" onclick="return copyToClipboard('')"></span>
	<div class="contract-templates-form">

		<?php $form = ActiveForm::begin([
			'id' => 'form-contract-templates',
			'options' => ['data-pjax' => true,],
		]); ?>
		<div class="card-body">
			<div class="row">

				<div class="col-md-12">
					<?= Alert::widget([
						'options' => [
							'class' => 'alert-info alert-dismissible',
						],
						'closeButton' => false,
						'body' => "در تمام فیلد های شرح $typeTextFa و شرح بندهای $typeTextFa موارد زیر قابل استفاده هستند. <br>
امکان تعریف متغیر در متن وجود دارد و بعدا در زمان ایجاد $typeTextFa امکان تغییر متغیر ها وجود دارد <br>
برا تعریف متغیر میتوانید از علامت {} استفاده کنید, به عنوان مثال اگر متن زیر را در شرح قرار داد بنویسید: <br>
این $typeTextFa فیمابین شرکت {company_name} و {sex} {first_name} {last_name} منعقد میگردد. <br>
در زمان ایجاد $typeTextFa امکان تعیین متغییر هارا خواهید داشت و در $typeTextFa به عنوان مثال متن به صورت زیر نمایش داده میشود: <br>
این $typeTextFa فیمابین شرکت آواپرداز و آقای محمدعلی شبانی منعقد میگردد. <br> <br>
متغییر های زیر از قبل تعریف شده و قابل استفاده هستند: <br>" . $variables .
"<br><br> <b>نکته:</b> متغیر های از پیش تعریف شده مربوط به شرکت مثل نام شرکت, شناسه ملی و ... از طریق تنظیمات باید تعریف شوند.",
					]) ?>
				</div>

				<div class="col-md-12">
					<?= $form->field($model, 'title')->textInput() ?>
				</div>

				<div class="col-md-12">
					<?= $form->field($model, 'description')->widget(CKEditorWidget::class, [
						'options' => [
							'class' => 'form-control description-fields',
							'onchange' => "return changeContractTemplateDescription(this, " . json_encode($model->variables) . ");",
						],
					]) ?>
				</div>

				<div class="col-md-12">
					<div id="contract-variables" class="row">

					</div>
				</div>

				<div class="col-md-12">
					<?= $form->field($model, 'signatures')->widget(CKEditorWidget::class, [
						'options' => [
							'class' => 'form-control description-fields',
							'onchange' => "return changeContractTemplateDescription(this, " . json_encode($model->variables) . ");",
						],
					]) ?>
				</div>

			</div>


			<?php
            if (is_array($model->clausesModels) && count($model->clausesModels)):
            DynamicFormWidget::begin([
				'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
				'widgetBody' => '.container-items', // required: css class selector
				'widgetItem' => '.form-field', // required: css class
				'limit' => 999, // the maximum times, an element can be cloned (default 999)
				'min' => 1, // 0 or 1 (default 1)
				'insertButton' => '.add-field', // css class
				'deleteButton' => '.remove-field', // css class
				'model' => $model->clausesModels[0],
				'formId' => 'form-contract-templates',
				'formFields' => [
					'title',
					'description',
				],
			]);
            ?>
			<div id="fields-wrapper" class="card material-card mb-3">
				<div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-clipboard-list-check fa-lg"></i>
                    <?= Module::t('module', "$model->typeText Clauses") ?>
                </span>
					<button type="button" class="add-field btn btn-success btn-xs">
						<i class="fas fa-plus"></i>
						<?= Module::t('module', "Add $model->typeText Clause") ?>
					</button>
				</div>
				<div class="card-body container-items"><!-- widgetContainer -->
					<?php foreach ($model->clausesModels as $i => $modelClause):
						/** @var ContractClausesModel $modelClause */
						?>
						<div class="form-field card material-card border-info mb-3"><!-- widgetBody -->
							<div class="card-header  d-flex justify-content-between align-items-center <?= $i > 0 ? 'collapsed' : '' ?> heading-<?= $i ?>"
								 data-toggle="collapse"
								 data-target=".collapse-<?= $i ?>"
								 aria-expanded="<?= $i > 0 ? 'false' : 'true' ?>"
								 aria-controls=".collapse-<?= $i ?>">
                             <span class="panel-title-field text-bold text-dark">
                                <?= $modelClause->title ?? Module::t('module', "{0}: $model->typeText Clause", [0 => $i + 1]) ?>
                            </span>
								<button type="button" class="remove-field btn btn-danger btn-xs">
									<i class="fas fa-minus"></i>
								</button>
							</div>
							<div class="collapse <?= $i > 0 ? '' : 'show' ?> card-body collapse-<?= $i ?>"
								 aria-labelledby="heading-<?= $i ?>" data-parent="#fields-wrapper">
								<div class="row">
									<div class="col-sm-12">
										<?= $form->field($modelClause, "[{$i}]title")->textInput(['maxlength' => true]) ?>
									</div>
									<div class="col-sm-12">
										<?= $form->field($modelClause, "[{$i}]description")->textarea(
											[
												'rows' => 6,
												'class' => 'description-fields description-fields-clauses form-control',
												'onkeyup' => "return changeContractTemplateDescription(this, " . json_encode($model->variables) . ", $i);",
												'onchange' => "return changeContractTemplateDescription(this, " . json_encode($model->variables) . ", $i);",
											]
										) ?>
									</div>

									<div class="col-md-12">
										<div id="contract-clauses-variables-div-<?= $i ?>"
											 class="row contract-clauses-variables-div">

										</div>
									</div>
								</div><!-- end:row -->

							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php
            DynamicFormWidget::end();
            endif;
            ?>


		</div>
		<div class="card-footer">
			<?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

<?php

$fieldText = Module::t('module', "$model->typeText Clause:");
$optionText = Module::t('module', 'Option:');

$variables = json_encode($model->variables);
$js = <<< JS

    $(document).ready(function () {
        jQuery(".description-fields").each(function(index) {
        	$(this).trigger('change');
    	});
    });


jQuery('.container-items .select-type').on('change', function(event){
    resetFields(this);
});
   
$( '.container-items .select-type' ).trigger( "change" );
 
jQuery(".dynamicform_wrapper").on("beforeInsert", function(e, addButton) {
    addButton.prop('disabled', true);
});

jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    
    jQuery(".contract-clauses-variables-div").each(function(index) {
        $(this).attr('id', 'contract-clauses-variables-div-'+index)
    });
    
        jQuery(".description-fields-clauses").each(function(index) {
        $(this).attr('onkeyup', 'return changeContractTemplateDescription(this, $variables, '+index+');');
        $(this).attr('onchange', 'return changeContractTemplateDescription(this, $variables, '+index+');');
    });
    
    jQuery(".dynamicform_wrapper .panel-title-field").each(function(index) {
        jQuery(this).html("{$fieldText} " + (index + 1));
        jQuery(this).closest(".card-header").attr({
        'data-target' : ".collapse-" + (index), 
        'aria-controls' : ".collapse-" + (index),
        'aria-expanded' : "false"
        }).removeClass (function (index, className) {
            return (className.match (/(^|\s)heading-\S+/g) || []).join(' ');
        }).addClass("heading-" + (index));
    });
    
    jQuery(".dynamicform_wrapper .collapse").each(function(index) {
        jQuery(this).removeClass (function (index, className) {
            return (className.match (/(^|\s)collapse-\S+/g) || []).join(' ');
        }).addClass("collapse-" + (index))
        .attr('aria-labelledby', ".heading-" + (index)).removeClass('show');
    });
    
    $('.collapse-'+(jQuery(".dynamicform_wrapper .collapse").length - 1)).collapse('show');
    
    jQuery('.container-items .select-type').on('change', function(event){
        resetFields(this); // lib.js
    });
    $( '.container-items .select-type' ).trigger( "change" );
    
    attachFormOptionsEvents();
    
    setTimeout(function() {
        $('.add-field').prop('disabled', false);
    }, 1000);

});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-field").each(function(index) {
        jQuery(this).html("{$fieldText} " + (index + 1))
    });
    attachFormOptionsEvents();
});

function attachFormOptionsEvents() {
     jQuery(".dynamicform_inner").on("afterInsert", function(e, item) {
        jQuery(item).closest('.features').find('.panel-title-field-feature').each(function(index) {
            jQuery(this).html("{$optionText} " + (index + 1))
    });
    
    
        attachFormOptionsSortable(); // Defined in _form-options view
});

jQuery(".dynamicform_inner").on("afterDelete", function(e) {
    jQuery(".dynamicform_inner .features").each(function() {
        jQuery(this).find('.panel-title-field-feature').each(function(index) {
            jQuery(this).html("{$optionText} " + (index + 1))
        });
    });
}); 
}

function resetFields(element) {
    type = $(element).val();
    formField = $(element).closest('.form-field');

    if ($.inArray(type, ["check_list", "dropdown", "radio"]) > -1) {
        formField.find('.features').css('display', 'block');
    } else {
        formField.find('.features').css('display', 'none');
        formField.find('.features').find(':input:text').val('');
        formField.find('.features').find(':checkbox, :radio').prop('checked', false).removeAttr('checked');
        formField.find('.features').find('.remove-option').trigger('click');
    }
    $('#dynamic-form-builder').yiiActiveForm('resetForm');
}

JS;

$this->registerJs($js, yii\web\View::POS_READY);