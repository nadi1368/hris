<?php

namespace hesabro\hris\models;

use hesabro\helpers\traits\ModelHelper;
use hesabro\hris\Module;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Model;

class EmployeeExperienceBase extends Model
{
    use ModelHelper;

    public mixed $uuid = '';

    public $institute;

    public $start_at;

    public $end_at;

    public $post;

    public $isNewRecord = false;

    public $deleted = false;

    public $added = false;

    public function init()
    {
        parent::init();

        $this->uuid = $this->uuid ?: Uuid::uuid4()->toString();
    }

    public function beforeValidate()
    {
        $this->deleted = (bool) ((int) $this->deleted);
        $this->added = (bool) ((int) $this->added);
        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        parent::afterValidate();

        $this->uuid = $this->uuid ?: Uuid::uuid4()->toString();
    }

    public function rules()
    {
        return [
            [['institute', 'start_at', 'end_at', 'post'], 'required'],
            [['institute', 'post', 'uuid'], 'string'],
            [['deleted', 'added'], 'boolean'],
            [['start_at', 'end_at'], 'validateDate']
        ];
    }

    public function validateDate($attribute, $params): void
    {
        if (is_null(Yii::$app->jdf::jalaliToTimestamp(Yii::$app->jdf::tr_num($this->$attribute), 'Y/m/d'))) {
            $this->addError($attribute, Module::t('module', 'Invalid Date'));
        }
    }

    public function attributeLabels()
    {
        return [
            'institute' => Module::t('module', 'Name') . ' ' . Module::t('module', 'Corporation') . ' ' . Module::t('module', 'Or') . ' ' . Module::t('module', 'Institute'),
            'start_at' => Module::t('module', 'Start Work Date'),
            'end_at' => Module::t('module', 'End Work Date'),
            'post' => Module::t('module', 'Post')
        ];
    }

    public function getPendingDataHint(string $attribute, EmployeeBranchUser $model, bool $mainValue = false, string $default = null): array
    {
        if ($this->deleted || $this->added) {
            return [
                implode(' ', [
                    Module::t('module', 'Pending Value'),
                    Module::t('module', $this->deleted ? 'Delete' : 'Add')
                ]),
                ['class' => 'profile-input-hint' . ($this->deleted ? ' text-danger' : ' text-success')]
            ];
        }

        return $model->getPendingDataHint($attribute, $mainValue, $default);
    }

    public function beforeSave()
    {
        $this->deleted = false;
        $this->added = false;
    }

    public function diff(EmployeeExperience $employeeExperience): array
    {

        $fields = [];
        foreach (['institute', 'start_at', 'end_at', 'post'] as $field) {

            if ($this->$field !== $employeeExperience->$field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}