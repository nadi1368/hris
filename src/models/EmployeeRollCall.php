<?php

namespace hesabro\hris\models;

use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\hris\Module;
use Yii;

/**
 * This is the model class for table "{{%employee_rollcall}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $date
 * @property string $status
 * @property float $total
 * @property float $shift
 * @property int $over_time
 * @property int $low_time
 * @property int $mission_time
 * @property int $leave_time
 * @property float $in_1
 * @property float $out_1
 * @property float $in_2
 * @property float $out_2
 * @property float $in_3
 * @property float $out_3
 * @property int $t_id
 * @property int $period_id
 *
 * @property object $user
 */
class EmployeeRollCall extends \yii\db\ActiveRecord
{

    const SCENARIO_INSERT = 'insert';
    public EmployeeBranchUser $employee;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_rollcall}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['user_id', 'date', 'status', 'total', 'shift', 'over_time', 'low_time', 'in_1', 'out_1', 'in_2', 'out_2', 'in_3', 'out_3', 't_id'], 'required'],
            [['user_id', 't_id'], 'integer'],
            [['total', 'shift', 'over_time', 'low_time', 'in_1', 'out_1', 'in_2', 'out_2', 'in_3', 'out_3'], 'default', 'value' => '00::00'],
            [['over_time', 'low_time', 'mission_time', 'leave_time'], 'default', 'value' => 0],
            [['date'], 'string', 'max' => 10],
            [['status'], 'string', 'max' => 128],
            [['status'], 'compare', 'compareValue' => 'ورود و خروج ناقص', 'operator' => '!=', 'type' => 'string'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_INSERT] = ['user_id', 'date', 'status', 'total', 'shift', 'over_time', 'low_time', 'mission_time', 'leave_time', 'in_1', 'out_1', 'in_2', 'out_2', 'in_3', 'out_3', 't_id', 'period_id'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'user_id' => Module::t('module', 'User ID'),
            'date' => Module::t('module', 'Date'),
            'status' => Module::t('module', 'Status'),
            'total' => 'کارکرد',
            'shift' => 'شیفت',
            'over_time' => 'اضافه کار',
            'low_time' => 'کسر کار',
            'mission_time' => 'ماموریت',
            'leave_time' => 'مرخصی ساعتی',
            'in_1' => 'ورود ۱',
            'out_1' => 'خروج ۱',
            'in_2' => 'ورود ۲',
            'out_2' => 'خروج ۲',
            'in_3' => 'ورود ۳',
            'out_3' => 'خروج ۳',
            't_id' => 'ای دی دستگاه',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'update_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return EmployeeRollCallQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmployeeRollCallQuery(get_called_class());
    }


    /**
     * @param $time 01:22
     * @return int 88
     */
    public static function convertToMinutes($time): int
    {
        if (strpos($time, ':') == false) {
            return 0;
        }
        $time = \Yii::$app->phpNewVer->trim($time, "﻿");
        list($hours, $minutes) = explode(':', $time);
        return (int)(((int)$hours * 60) + (int)$minutes);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ]
        ];
    }

    /**
     * @return string
     */
    public function getStatusCssClass()
    {
        if ($this->status == "غیبت") {
            return 'danger';
        }
        if ($this->low_time > 0) {
            return 'warning';
        }
        if ($this->status == "ورود و خروج کامل") {
            return 'success';
        }
        return '';
    }


    public function beforeSave($insert)
    {
        if ($this->getScenario() == self::SCENARIO_INSERT) {
            if ($this->mission_time > 0 || $this->leave_time > 0) {
                $total = self::convertToMinutes($this->total);
                $shift = self::convertToMinutes($this->shift);
                $calTotal = $total + $this->mission_time + $this->leave_time + $this->low_time - $this->over_time;
                if ($calTotal != $shift) {
                    $this->low_time -= ($calTotal - $shift);
                    if ($this->low_time < 0) {
                        $this->over_time += abs($this->low_time);
                        $this->low_time = 0;
                    }
                }
            }
            if ($this->employee->shift == EmployeeBranchUser::SHIFT_TOW && $this->shift == "08:00" && ((int)$this->total) > 0) {
                $this->low_time -= 30;
                if ($this->low_time < 0) {
                    $this->over_time += abs($this->low_time);
                    $this->low_time = 0;
                }
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
    public function saveLowTime(): bool
    {
        if ($this->low_time > 0) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_LOW_TIME,
                'type' => SalaryItemsAddition::TYPE_LOW_DELAY,
                'from_date' => $this->date,
                'second' => $this->low_time,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'period_id' => $this->period_id
            ]);
            return $model->save();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function saveOverTime(): bool
    {
        if ($this->over_time > 0) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_OVER_TIME,
                'type' => $this->status == 'تعطیل' ? SalaryItemsAddition::TYPE_OVER_TIME_HOLIDAY : SalaryItemsAddition::TYPE_OVER_TIME_DAY,
                'from_date' => $this->date,
                'second' => $this->over_time,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'period_id' => $this->period_id
            ]);
            return $model->save();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function saveLeaveTime(): bool
    {
        if ($this->leave_time > 0) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_LEAVE_HOURLY,
                'type' => SalaryItemsAddition::TYPE_LEAVE_MERIT_HOURLY,
                'from_date' => $this->date,
                'second' => $this->leave_time,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'period_id' => $this->period_id
            ]);
            return $model->save();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function saveLeaveDay(): bool
    {
        if (strpos($this->status, 'استحقاقی') !== false) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_LEAVE_DAILY,
                'type' => SalaryItemsAddition::TYPE_LEAVE_MERIT_DAILY,
                'from_date' => $this->date,
                'to_date' => Yii::$app->jdf::plusDay($this->date, 1),
                'second' => $this->leave_time,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'period_id' => $this->period_id
            ]);
            return $model->save();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function saveAbsentDay(): bool
    {
        if (strpos($this->status, 'غیبت') !== false && ($lowTime = EmployeeRollCall::convertToMinutes($this->shift) - $this->mission_time) > 0) {
            $model = new SalaryItemsAddition([
                'scenario' => SalaryItemsAddition::SCENARIO_CREATE_AUTO,
                'user_id' => $this->user_id,
                'kind' => SalaryItemsAddition::KIND_LOW_TIME,
                'type' => SalaryItemsAddition::TYPE_LOW_ABSENCE,
                'from_date' => $this->date,
                'to_date' => Yii::$app->jdf::plusDay($this->date, 1),
                'second' => $lowTime,
                'description' => 'ثبت خودکار از دستگاه حضور و غیاب',
                'status' => SalaryItemsAddition::STATUS_CONFIRM,
                'period_id' => $this->period_id
            ]);
            return $model->save();
        }
        return true;
    }
}
