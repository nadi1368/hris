<?php

use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\SalaryInsurance;
use hesabro\helpers\widgets\DynamicFormWidget;
use hesabro\hris\Module;
use yii\bootstrap4\ActiveForm;
use common\models\Customer;
use yii\web\View;
use yii\widgets\MaskedInput;

/**
 * @var View $this
 * @var EmployeeBranchUser $model
 * @var ActiveForm $form
 * @var bool $isAdmin
 */

$datepickerDefaultOptions = [
    'options' => [
        'locale' => [
            'format' => 'jYYYY/jMM/jDD',
        ],
        'drops' => 'down',
        'opens' => 'right',
        'jalaali' => true,
        'showDropdowns' => true,
        'language' => 'fa',
        'singleDatePicker' => true,
        'useTimestamp' => true,
        'timePicker' => false,
        'timePickerSeconds' => true,
        'timePicker24Hour' => true
    ],
    'htmlOptions' => [
        'class' => 'form-control date-input',
        'autocomplete' => 'off'
    ]
];
$this->beginBlock('information');
$marriageClass = ((int)$model->marital) !== EmployeeBranchUser::MARITAL_MARRIED ? 'hide' : '';
$scenarioAttributes = $model->scenarios()[$model->scenario] ?? [];
?>

<div class="card-body">
    <div class="row">
        <?php if (!$isAdmin && $model->reject_update_description && !$model->reject_update_description_seen): ?>
            <div class="col-12">
                <div class="alert alert-danger d-flex align-items-center justify-content-between">
                    <div>
                        <p>آخرین درخواست شما برای تغییر اطلاعات کاربری رد شد.</p>
                        <p class="mb-0"><?= $model->reject_update_description ?></p>
                    </div>
                    <button type="button" id="dismissRejectUpdate" class="dismiss-reject-update">
                        <i class="fas fa-times text-danger"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!$isAdmin && $model->isConfirmed): ?>
            <div class="col-12">
                <div class="alert alert-warning">
                    <p>وضعیت حساب کاربری شما تایید شده است، اگر ویرایشی در این صفحه صورت گیرد بعد از تایید مدیریت اعمال
                        می‌شود.</p>
                    <p class="mb-0"><strong>توجه:</strong> هر ویرایش جدیدی، ویرایش تایید نشده قبل را باطل می‌کند.</p>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-12 col-md-3 d-flex flex-column justify-content-center align-items-center p-3">
            <div id="avatar-container" class="p-2">
                <?php if ($avatar = $model->user->getFileUrl('avatar')): ?>
                    <img src="<?= $avatar ?>" alt="avatar" width="80" height="80" class="rounded-circle"
                         style="object-fit: cover; object-position: center;"/>
                <?php else: ?>
                    <i class="fal fa-user-circle fa-6x"></i>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-sm btn-info" data-file-browser="avatar"><?= Module::t('module', 'Choose') ?></button>
            <?= $form->field($model, 'avatar')->fileInput([
                'data' => [
                    'file-input' => 'avatar',
                    'onload' => 'onAvatarLoad'
                ],
                'class' => 'd-none'
            ])->label(false) ?>
        </div>

        <div class="col-12 col-md-9">
            <div class="row">
                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'first_name')
                        ->textInput(['maxlength' => true, 'value' => $model->getAttributeValue('first_name', $isAdmin)])
                        ->hint(...$model->getPendingDataHint('first_name', $isAdmin)) ?>
                </div>

                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'last_name')
                        ->textInput(['maxlength' => true, 'value' => $model->getAttributeValue('last_name', $isAdmin)])
                        ->hint(...$model->getPendingDataHint('last_name', $isAdmin)) ?>
                </div>

                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'father_name')
                        ->textInput(['maxlength' => true, 'value' => $model->getAttributeValue('father_name', $isAdmin)])
                        ->hint(...$model->getPendingDataHint('father_name', $isAdmin)) ?>
                </div>

                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'sex')
                        ->dropDownList(
                            Customer::itemAlias('Sex'),
                            [
                                'id' => 'sex-select',
                                'prompt' => Module::t('module', 'Select...'),
                                'value' => $model->getAttributeValue('sex', $isAdmin)
                            ]
                        )->hint(...$model->getPendingDataHint('sex', $isAdmin)) ?>
                </div>

                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'nationalCode')
                        ->textInput(['maxlength' => true, 'value' => $model->getAttributeValue('nationalCode', $isAdmin)])
                        ->hint(...$model->getPendingDataHint('nationalCode', $isAdmin)) ?>
                </div>

                <div class="col-12 col-md-4 date-input">
                    <?= $form->field($model, 'birthday')
                        ->widget(MaskedInput::class, [
                            'mask' => '9999/99/99',
                            'options' => [
                                'value' => $model->getAttributeValue('birthday', $isAdmin),
                                'id' => "employeebranchuser-birthday",
                                'autocomplete' => 'off'
                            ]
                        ])->hint(...$model->getPendingDataHint('birthday', $isAdmin)) ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'national')
                ->dropDownList(
                    Customer::itemAlias('National'),
                    [
                        'prompt' => Module::t('module', "Select"),
                        'value' => $model->getAttributeValue('national', $isAdmin)
                    ]
                )->hint(...$model->getPendingDataHint('national', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'sh_number')
                ->textInput(['maxlength' => true, 'value' => $model->getAttributeValue('sh_number', $isAdmin)])
                ->hint(...$model->getPendingDataHint('sh_number', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3 date-input">
            <?= $form->field($model, 'issue_date')
                ->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                    'options' => [
                        'value' => $model->getAttributeValue('issue_date', $isAdmin),
                        'id' => "employeebranchuser-issue_date",
                        'autocomplete' => 'off'
                    ]
                ])->hint(...$model->getPendingDataHint('issue_date', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'issue_place')
                ->textInput(['maxlength' => true, 'value' => $model->getAttributeValue('issue_place', $isAdmin)])
                ->hint(...$model->getPendingDataHint('issue_place', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'education')
                ->dropDownList(
                    EmployeeBranchUser::itemAlias('education'),
                    [
                        'prompt' => Module::t('module', 'Select...'),
                        'value' => $model->getAttributeValue('education', $isAdmin)
                    ]
                )->hint(...$model->getPendingDataHint('education', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'marital')
                ->dropDownList(
                    EmployeeBranchUser::itemAlias('marital'),
                    [
                        'id' => 'marital-select',
                        'prompt' => Module::t('module', 'Select...'),
                        'value' => $model->getAttributeValue('marital', $isAdmin)
                    ]
                )->hint(...$model->getPendingDataHint('marital', $isAdmin)) ?>
        </div>

        <div id="dateOfMarriage" class="col-12 col-md-3 date-input <?= $marriageClass ?>">
            <?= $form->field($model, 'date_of_marriage')
                ->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                    'options' => [
                        'value' => $model->getAttributeValue('date_of_marriage', $isAdmin),
                        'id' => "employeebranchuser-date_of_marriage",
                        'autocomplete' => 'off'
                    ]
                ])->hint(...$model->getPendingDataHint('date_of_marriage', $isAdmin)) ?>
        </div>

        <div id="childCount" class="col-12 col-md-3 <?= $marriageClass ?>">
            <?= $form->field($model, 'child_count')
                ->textInput(['type' => 'number', 'value' => $model->getAttributeValue('child_count', $isAdmin)])
                ->hint(...$model->getPendingDataHint('child_count', $isAdmin)) ?>
        </div>

        <?php if ($isAdmin): ?>
            <div class="col-12 col-md-3">
                <?= $form->field($model, 'count_insurance_addition')->textInput(['type' => 'number'])->hint('تعداد نفرات برای کسر بیمه تکمیلی') ?>
            </div>
            <div class="col-12 col-md-3">
                <?= $form->field($model, 'job_code')
                    ->dropDownList(SalaryInsurance::itemAlias('List'), ['prompt' => Module::t('module', 'Select...')]) ?>
            </div>
        <?php endif; ?>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'insurance_history_month_count')
                ->textInput([
                    'type' => 'number',
                    'value' => $model->getAttributeValue('insurance_history_month_count', $isAdmin)
                ])->hint(...$model->getPendingDataHint('insurance_history_month_count', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'work_history_day_count')
                ->textInput([
                    'type' => 'number',
                    'value' => $model->getAttributeValue('work_history_day_count', $isAdmin)
                ])->hint(...$model->getPendingDataHint('work_history_day_count', $isAdmin)) ?>
        </div>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'insurance_code')
                ->textInput(['disabled' => !$isAdmin]) ?>
        </div>

        <div class="col-12 col-md-3 <?= $isAdmin ? 'date-input' : '' ?>">
            <?= $form->field($model, 'start_work')
                ->widget(MaskedInput::class, [
                    'mask' => '9999/99/99',
                    'options' => [
                        'id' => "employeebranchuser-start_work",
                        'autocomplete' => 'off',
                        'disabled' => !$isAdmin
                    ]
                ]) ?>
        </div>


        <?php if ($isAdmin): ?>
            <div class="col-12 col-md-3 date-input">
                <?= $form->field($model, 'end_work')
                    ->widget(MaskedInput::class, [
                        'mask' => '9999/99/99',
                        'options' => [
                            'id' => "employeebranchuser-end_work",
                            'autocomplete' => 'off'
                        ]
                    ]) ?>
            </div>
        <?php endif; ?>

        <div class="col-12 col-md-3">
            <?= $form->field($model, 'email')
                ->textInput([
                    'type' => 'email',
                    'value' => $model->getAttributeValue('email', $isAdmin)
                ])->label(Module::t('module', 'Email') . ' ' . Module::t('module', 'Organizational'))
                ->hint(...$model->getPendingDataHint('email', $isAdmin)) ?>
        </div>

        <?php if ($isAdmin): ?>
            <div class="col-12">
                <?= $form->field($model, 'description_work')->textarea(['rows' => 1]) ?>
            </div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <div class="col-12">
                <?= $form->field($model, 'work_address')->textarea(['rows' => 1]) ?>
            </div>
        <?php endif; ?>

        <div class="col-12">
            <?= $form->field($model, 'employee_address')
                ->textarea(['rows' => 1, 'value' => $model->getAttributeValue('employee_address', $isAdmin)])
                ->label(Module::t('module', 'Address'))
                ->hint(...$model->getPendingDataHint('employee_address', $isAdmin)) ?>
        </div>

        <div id="children" class="col-12 mt-2 mb-2 py-3 <?= $marriageClass ?>">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'employee_children_dynamic_form',
                'widgetBody' => '.employee-children',
                'widgetItem' => '.employee-child',
                'limit' => 10,
                'min' => 0,
                'insertButton' => '.add-child',
                'deleteButton' => '.remove-child',
                'model' => $model->children[0],
                'formId' => 'ajax-form-employee-update-profile',
                'formFields' => [
                    'name', 'birthday'
                ],
            ]); ?>

            <div class="w-100 d-flex items-center justify-content-between gap-2">
                <label><?= Module::t('module', 'Children Information') ?></label>
                <button type="button" class="btn btn-success add-child btn-xs" style="border-radius: 4px !important;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <div class="employee-children">

                <?php foreach ($model->children as $childIndex => $child): ?>
                    <div class="employee-child row"
                         style="background-color: <?= $child->added ? '#dff0d8' : ($child->deleted ? '#f2dede' : 'transparent') ?>">
                        <?= $form->field($child, "[$childIndex]uuid")->hiddenInput(['value' => $child->uuid])->label(false); ?>
                        <?= $form->field($child, "[$childIndex]deleted")->hiddenInput(['value' => $child->deleted])->label(false); ?>
                        <?= $form->field($child, "[$childIndex]added")->hiddenInput(['value' => $child->added])->label(false); ?>

                        <div class="col-12 col-md-3">
                            <?= $form->field($child, "[$childIndex]name")
                                ->textInput(['value' => $model->getAttributeValue("children.$childIndex.name", $isAdmin)])
                                ->hint(...$child->getPendingDataHint("children.$childIndex.name", $model, $isAdmin)) ?>
                        </div>
                        <div class="col-12 col-md-3">
                            <?= $form->field($child, "[$childIndex]birthday")
                                ->widget(dateRangePicker::class, array_merge($datepickerDefaultOptions, [
                                    'htmlOptions' => [
                                        'id' => "employeechild-$childIndex-birthday",
                                        'class' => 'form-control date-input bg-white',
                                        'autocomplete' => 'off',
                                        'readonly' => true,
                                        'value' => $model->getAttributeValue("children.$childIndex.birthday", $isAdmin)
                                    ]
                                ]))->hint(...$child->getPendingDataHint("children.$childIndex.birthday", $model, $isAdmin)) ?>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-start pt-4">
                            <?= $form->field($child, "[$childIndex]insurance")
                                ->checkbox(['checked' => $model->getAttributeValue("children.$childIndex.insurance", $isAdmin)])
                                ->hint(...$child->getPendingDataHint("children.$childIndex.insurance", $model, $isAdmin)) ?>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-end pt-2">
                            <button type="button" class="btn btn-danger remove-child btn-xs" style="border-radius: 4px !important;">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>

        <div class="col-12 mt-2 mb-2 py-3">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'employee_experiences_dynamic_form',
                'widgetBody' => '.employee-experiences',
                'widgetItem' => '.employee-experience',
                'limit' => 10,
                'min' => 0,
                'insertButton' => '.add-experience',
                'deleteButton' => '.remove-experience',
                'model' => $model->experiences[0],
                'formId' => 'ajax-form-employee-update-profile',
                'formFields' => [
                    'institute', 'start_at', 'end_at', 'post'
                ],
            ]); ?>

            <div class="w-100 d-flex items-center justify-content-between gap-2">
                <label><?= Module::t('module', 'Work Experiences') ?></label>
                <button type="button" class="btn btn-success add-experience btn-xs" style="border-radius: 4px !important;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <div class="employee-experiences">
                <?php foreach ($model->experiences as $idx => $experience): ?>
                    <div class="employee-experience row"
                         style="background-color: <?= $experience->added ? '#dff0d8' : ($experience->deleted ? '#f2dede' : 'transparent') ?>">
                        <?= $form->field($experience, "[$idx]uuid")->hiddenInput(['value' => $experience->uuid])->label(false); ?>
                        <?= $form->field($experience, "[$idx]deleted")->hiddenInput(['value' => $experience->deleted])->label(false); ?>
                        <?= $form->field($experience, "[$idx]added")->hiddenInput(['value' => $experience->added])->label(false); ?>

                        <div class="col-12 col-md-3">
                            <?= $form->field($experience, "[$idx]institute")
                                ->textInput(['value' => $model->getAttributeValue("experiences.$idx.institute", $isAdmin)])
                                ->hint(...$experience->getPendingDataHint("experiences.$idx.institute", $model, $isAdmin)) ?>
                        </div>

                        <div class="col-12 col-md-3">
                            <?= $form->field($experience, "[$idx]start_at")
                                ->widget(dateRangePicker::class, array_merge($datepickerDefaultOptions, [
                                    'htmlOptions' => [
                                        'id' => "employeeexperience-$idx-start_at",
                                        'class' => 'form-control date-input bg-white',
                                        'autocomplete' => 'off',
                                        'readonly' => true,
                                        'value' => $model->getAttributeValue("experiences.$idx.start_at", $isAdmin)
                                    ]
                                ]))->hint(...$experience->getPendingDataHint("experiences.$idx.start_at", $model, $isAdmin)) ?>
                        </div>

                        <div class="col-12 col-md-3">
                            <?= $form->field($experience, "[$idx]end_at")
                                ->widget(dateRangePicker::class, array_merge($datepickerDefaultOptions, [
                                    'htmlOptions' => [
                                        'id' => "employeeexperience-$idx-end_at",
                                        'class' => 'form-control date-input bg-white',
                                        'autocomplete' => 'off',
                                        'readonly' => true,
                                        'value' => $model->getAttributeValue("experiences.$idx.end_at", $isAdmin)
                                    ]
                                ]))->hint(...$experience->getPendingDataHint("experiences.$idx.end_at", $model, $isAdmin)) ?>
                        </div>

                        <div class="col-12 col-md-2">
                            <?= $form->field($experience, "[$idx]post")
                                ->textInput(['value' => $model->getAttributeValue("experiences.$idx.post", $isAdmin)])
                                ->hint(...$experience->getPendingDataHint("experiences.$idx.post", $model, $isAdmin)) ?>
                        </div>

                        <div class="col-12 col-md-1 d-flex align-items-center justify-content-end pt-2">
                            <button type="button" class="btn btn-danger remove-experience btn-xs" style="border-radius: 4px !important;">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>
</div>

<?php $this->endBlock(); ?>
