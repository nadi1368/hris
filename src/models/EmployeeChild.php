<?php

namespace hesabro\hris\models;

use common\components\jdf\Jdf;
use common\models\Model;
use Ramsey\Uuid\Uuid;
use Yii;

class EmployeeChild extends Model
{
    public mixed $uuid = '';

    public mixed $name = '';

    public mixed $birthday = '';

    public mixed $insurance = false;

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
        $this->insurance = (bool) ((int) $this->insurance);
        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        parent::afterValidate();

        $this->uuid = $this->uuid ?: Uuid::uuid4()->toString();
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['name', 'birthday', 'insurance'], 'required'],
            [['name', 'uuid'], 'string'],
            [['insurance', 'deleted', 'added'], 'boolean'],
            ['birthday', 'validateBirthday']
        ]);
    }

    public function validateBirthday($attribute, $params): void
    {
        if (is_null(Jdf::jalaliToTimestamp(Jdf::tr_num($this->birthday), 'Y/m/d'))) {
            $this->addError($attribute, Yii::t('app', 'Invalid Date'));
        }
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'birthday' => Yii::t('app', 'Birthday'),
            'insurance' => Yii::t('app', 'Include Child Insurance'),
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

    public static function itemAlias($type, $code = null)
    {
        $items = [
            'insurance' => [
                Yii::t('app', 'Do Not Include Child Insurance'),
                Yii::t('app', 'Include Child Insurance'),
            ],
        ];

        return $code ? ($items[$type][$code] ?? false) : ($items[$type] ?? false);
    }

    public function beforeSave()
    {
        $this->deleted = false;
        $this->added = false;
    }

    public function diff(EmployeeChild $employeeChild): array
    {

        $fields = [];
        foreach (['uuid', 'name', 'birthday', 'insurance'] as $field) {

            if ($this->$field !== $employeeChild->$field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}