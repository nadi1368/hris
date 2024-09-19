<?php

namespace hesabro\hris\models;

use common\components\jdf\Jdf;
use common\models\Model;
use Ramsey\Uuid\Uuid;
use Yii;

class EmployeeExperience extends Model
{
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
        if (is_null(Jdf::jalaliToTimestamp(Jdf::tr_num($this->$attribute), 'Y/m/d'))) {
            $this->addError($attribute, Yii::t('app', 'Invalid Date'));
        }
    }

    public function attributeLabels()
    {
        return [
            'institute' => Yii::t('app', 'Name') . ' ' . Yii::t('app', 'Corporation') . ' ' . Yii::t('app', 'Or') . ' ' . Yii::t('app', 'Institute'),
            'start_at' => Yii::t('app', 'Start Work Date'),
            'end_at' => Yii::t('app', 'End Work Date'),
            'post' => Yii::t('app', 'Post')
        ];
    }

    public function getPendingDataHint(string $attribute, EmployeeBranchUser $model, bool $mainValue = false, string $default = null): array
    {
        if ($this->deleted || $this->added) {
            return [
                implode(' ', [
                    Yii::t('app', 'Pending Value'),
                    Yii::t('app', $this->deleted ? 'Delete' : 'Add')
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