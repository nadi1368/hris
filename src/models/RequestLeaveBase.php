<?php

namespace hesabro\hris\models;

use hesabro\hris\models\EmployeeBranch;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\hris\Module;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%request_leave}}".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $user_id
 * @property int $manager_id
 * @property int $type
 * @property string $description
 * @property int $from_date
 * @property int $to_date
 * @property int $status
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 *
 * @property EmployeeBranch $branch
 * @property object $user
 * @property object $manager
 * @property object $update
 */
class RequestLeaveBase extends \yii\db\ActiveRecord
{
    public $range;

    const STATUS_DELETED = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_CONFIRM_MANAGER_BRANCH = 2;
    const STATUS_REJECT_MANAGER_BRANCH = 3;
    const STATUS_CONFIRM_ADMIN = 4;
    const STATUS_REJECT_ADMIN = 5;

    const TYPE_MERIT_HOURLY = 1;
    const TYPE_MERIT_DAILY = 2;
    const TYPE_TREATMENT_DAILY = 4;
    const TYPE_NO_SALARY_HOURLY = 5;
    const TYPE_NO_SALARY_DAILY = 6;
    const TYPE_MISSION_HOURLY = 7;
    const TYPE_MISSION_DAILY = 8;


    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_REJECT = 'reject';
    const SCENARIO_CONFIRM = 'confirm';

    const MERIT_HOURLY_REQUESTS_PER_DAY = 4;

    public $rejectDescription='';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%request_leave}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'user_id', 'manager_id', 'type', 'description', 'range'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['branch_id', 'user_id', 'manager_id', 'type', 'from_date', 'to_date', 'status', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['description'], 'string'],
            [['range'], 'validateMeritHourly', 'skipOnError' => true, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE], 'when' => function ($model) {
                return $model->type == self::TYPE_MERIT_HOURLY;
            }],
            [['range'], 'validateMissionHourly', 'skipOnError' => true, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE], 'when' => function ($model) {
                return $model->type == self::TYPE_MISSION_HOURLY;
            }],

            [['range'], 'validateMeritDaily', 'skipOnError' => true, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE], 'when' => function ($model) {
                return in_array($model->type, array_keys(self::itemAlias('TypesDaily')));
            }],


            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeBranch::class, 'targetAttribute' => ['branch_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['user_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['manager_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['description', 'range', 'type', '!branch_id', '!user_id', '!manager_id'];
        $scenarios[self::SCENARIO_UPDATE] = ['description', 'range', '!type', '!branch_id', '!user_id', '!manager_id'];
        $scenarios[self::SCENARIO_REJECT] = ['!status'];
        $scenarios[self::SCENARIO_CONFIRM] = ['!status'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'branch_id' => Yii::t('app', 'Branch ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'manager_id' => Yii::t('app', 'Manager ID'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'from_date' => Yii::t('app', 'From Date'),
            'to_date' => Yii::t('app', 'To Date'),
            'status' => Yii::t('app', 'State'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'created' => Yii::t('app', 'Created'),
            'changed' => Yii::t('app', 'Changed'),
            'range' => Yii::t('app', 'Range'),
        ];
    }

    public function beforeValidate()
    {
        if ($this->getScenario() == self::SCENARIO_CREATE || $this->getScenario() == self::SCENARIO_UPDATE) {
            $date_range = $this->rangeToTimestampRange($this->range, "Y/m/d H:i:s", 1, " - ", 00, 00, 00);
            $this->from_date = $date_range['start'];
            $this->to_date = $date_range['end'];

            if (in_array($this->type, array_keys(self::itemAlias('TypesDaily')))) {
                $date_range = $this->rangeToTimestampRange($this->range, "Y/m/d", 1, " - ", 00, 00, 00, true);
                $this->from_date = $date_range['start'];
                $this->to_date = $date_range['end'];
            }
            if (empty($this->from_date) || empty($this->to_date)) {
                $this->addError('range', Yii::t('app', 'Invalid {value} .', ['value' => $this->getAttributeLabel('range')]));
            }
        }
        return parent::beforeValidate();
    }

    public function validateMeritHourly($attribute)
    {
        if (date('z', $this->from_date) !== date('z', $this->to_date)) {
            $this->addError($attribute, Yii::t('app', 'Hourly leave can only be recorded in one day'));
        }

        $max_hours_leave = (int)self::MERIT_HOURLY_REQUESTS_PER_DAY;

        $sum = (($this->sumTodayLeaveHours() ?? 0)) - ($max_hours_leave * 60 * 60);
        $meritHourly_Requests = $sum <= 0 ? 1 : ceil(($sum / ($max_hours_leave * 60 * 60)) + 1);

        if (($this->to_date - $this->from_date) > ($max_hours_leave * 60 * 60)) {
            $this->addError($attribute, Yii::t('app', "Maximum Hourly Leave per request is:{max_hours_leave}", ['max_hours_leave' => $max_hours_leave]));
        }
        if ($meritHourly_Requests <= self::MERIT_HOURLY_REQUESTS_PER_DAY) {

            $merit_hours_leftover = (($max_hours_leave * 60 * 60) * self::MERIT_HOURLY_REQUESTS_PER_DAY) - (($this->sumTodayLeaveHours() ?? 0));

            if (($this->to_date - $this->from_date) > $merit_hours_leftover) {
                $this->addError($attribute, Yii::t('app', 'Your merit leave left for current request is  {merit_hour_leftover}', ['merit_hour_leftover' => Yii::$app->formatter->asDuration($merit_hours_leftover)]));
            }
        } else {
            $this->addError($attribute, Yii::t('app', 'Sum of Your Hourly Merit Leaves Hours are Reached to This day Limit'));
        }

    }

    public function validateMissionHourly($attribute)
    {
        if (date('z', $this->from_date) !== date('z', $this->to_date)) {
            $this->addError($attribute, Yii::t('app', 'Hourly leave can only be recorded in one day'));
        }
    }

    public function validateMeritDaily($attribute)
    {
        if ($this->to_date - $this->from_date <= 0) {
            $this->addError($attribute, Yii::t('app', "Minimum Range of Daily Leave is 1 Day and Days Start From daybreak"));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(EmployeeBranch::class, ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'manager_id']);
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
     * {@inheritdoc}
     * @return RequestLeaveQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new RequestLeaveQuery(get_called_class());
        return $query->active();
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool
    {
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->user_id == Yii::$app->user->id) {
            return true;
        }
        if ($this->status == self::STATUS_CONFIRM_MANAGER_BRANCH && Yii::$app->user->can('RequestLeave/admin-branch')) {
            return true;
        }
        if ($this->status == self::STATUS_CONFIRM_ADMIN && Yii::$app->user->can('RequestLeave/admin')) {
            return true;
        }
        return false;
    }

    public function canDelete()
    {
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->user_id == Yii::$app->user->id) {
            return true;
        }
        if ($this->status == self::STATUS_CONFIRM_MANAGER_BRANCH && Yii::$app->user->can('RequestLeave/admin-branch')) {
            return true;
        }
        if ($this->status == self::STATUS_CONFIRM_ADMIN && Yii::$app->user->can('RequestLeave/admin')) {
            return true;
        }
        return false;
    }

    public function canChangeStatus($new_status)
    {
        $can = false;
        if (Yii::$app->user->can('RequestLeave/admin-branch') || Yii::$app->user->can('RequestLeave/admin')) {
            switch ($new_status) {
                case self::STATUS_CONFIRM_MANAGER_BRANCH:
                case self::STATUS_REJECT_MANAGER_BRANCH:
                    if ($this->status == self::STATUS_WAIT_CONFIRM) {
                        $can = true;
                    }
                    break;
            }
        }
        if (Yii::$app->user->can('RequestLeave/admin')) {
            switch ($new_status) {
                case self::STATUS_CONFIRM_ADMIN:
                case self::STATUS_REJECT_ADMIN:
                    if ($this->status == self::STATUS_CONFIRM_MANAGER_BRANCH) {
                        $can = true;
                    }
                    break;
            }
        }
        return $can;
    }


    public function changeStatus($toStatus, $comment = "")
    {
        $this->status = $toStatus;
        $this->rejectDescription = $comment;
        return $this->save();
    }

    public function sumMeritLeaves($user_id = null)
    {
        $user_id = $user_id ?? Yii::$app->user->id;
        $month_start = $this->getStartAndEndOfCurrentMonth()['start'];
        $month_end = $this->getStartAndEndOfCurrentMonth()['end'];
        $year_start = $this->getStartAndEndOfYear()['start'];
        $year_end = $this->getStartAndEndOfYear()['end'];
        $notInArr = [
            self::STATUS_REJECT_ADMIN,
            self::STATUS_WAIT_CONFIRM,
            self::STATUS_REJECT_MANAGER_BRANCH
        ];

        $seconds['current_month'] = self::find()
            ->select(
                'SUM(CASE 
                    When to_date > ' . $month_end . ' AND from_date < ' . $month_end . ' AND from_date >=' . $month_start . ' THEN 1+' . $month_end . ' - from_date ' .
                '   When to_date > ' . $month_start . ' AND from_date < ' . $month_start . ' THEN to_date - ' . $month_start
                . ' When to_date <= 1+' . $month_end . ' AND from_date >=' . $month_start . ' THEN to_date - from_date
                 END)')
            ->andWhere(['in', 'type', [self::TYPE_MERIT_HOURLY, self::TYPE_MERIT_DAILY]])
            ->andWhere(['not in', 'status', $notInArr])
            ->andWhere(['user_id' => $user_id])
            ->scalar();

        $seconds['current_year'] = self::find()
            ->andWhere(['in', 'type', [self::TYPE_MERIT_HOURLY, self::TYPE_MERIT_DAILY]])
            ->andWhere(['not in', 'status', $notInArr])
            ->andWhere(['between', 'from_date', $year_start, $year_end])
            ->andWhere(['between', 'to_date', $year_start, $year_end])
            ->andWhere(['user_id' => $user_id])
            ->sum('to_date - from_date');

        return $seconds;
    }

    private function sumTodayLeaveHours()
    {
        $selected_day = Yii::$app->jdate->date("Y/m/d H:i:s", $this->from_date);

        $day_start_ts = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($selected_day) . " 00:00:00");
        $day_end_ts = strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($selected_day) . " 23:59:59");

        $notInArr = [self::STATUS_REJECT_ADMIN, self::STATUS_REJECT_MANAGER_BRANCH];
        $query = self::find()
            ->andWhere(['in', 'type', [self::TYPE_MERIT_HOURLY]])
            ->andWhere(['not in', 'status', $notInArr])
            ->andWhere(['between', 'from_date', $day_start_ts, $day_end_ts])
            ->andWhere(['user_id' => Yii::$app->user->id]);

        if ($this->scenario == self::SCENARIO_UPDATE) {
            $query->andWhere(['<>', 'id', $this->id]);
        }
        return $query->sum('to_date - from_date');
    }

    /*
    * حذف منطقی
    */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        if ($this->save()) {
            return true;
        } else {
            return false;
        }
    }


    public function beforeCreate()
    {
        if (($employeeBranch = EmployeeBranchUser::find()->andWhere(['user_id' => $this->user_id])->one()) !== null) {
            $this->branch_id = $employeeBranch->branch_id;
            $this->manager_id = $employeeBranch->branch->manager;
        } else {
            $this->branch_id = 8;
            $this->manager_id = 8;
        }
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
            'Types' => [
                self::TYPE_MERIT_HOURLY => Yii::t('app', 'Merit Hourly'),
                self::TYPE_MERIT_DAILY => Yii::t('app', 'Merit Daily'),
                self::TYPE_TREATMENT_DAILY => Yii::t('app', 'Treatment Daily'),
                self::TYPE_NO_SALARY_HOURLY => Yii::t('app', 'No Salary Hourly'),
                self::TYPE_NO_SALARY_DAILY => Yii::t('app', 'No Salary Daily'),
                self::TYPE_MISSION_HOURLY => Yii::t('app', 'Mission Hourly'),
                self::TYPE_MISSION_DAILY => Yii::t('app', 'Mission Daily'),
            ],
            'TypesDaily' => [
                self::TYPE_MERIT_DAILY => Yii::t('app', 'Merit Daily'),
                self::TYPE_TREATMENT_DAILY => Yii::t('app', 'Treatment Daily'),
                self::TYPE_NO_SALARY_DAILY => Yii::t('app', 'No Salary Daily'),
                self::TYPE_MISSION_DAILY => Yii::t('app', 'Mission Daily'),
            ],
            'TypesHourly' => [
                self::TYPE_MERIT_HOURLY => Yii::t('app', 'Merit Hourly'),
                self::TYPE_NO_SALARY_HOURLY => Yii::t('app', 'No Salary Hourly'),
                self::TYPE_MISSION_HOURLY => Yii::t('app', 'Mission Hourly'),
            ],
            'Status' => [
                self::STATUS_WAIT_CONFIRM => Yii::t('app', 'Wait Confirm'),
                self::STATUS_CONFIRM_MANAGER_BRANCH => Yii::t('app', 'Status Confirm Manager'),
                self::STATUS_REJECT_MANAGER_BRANCH => Yii::t('app', 'Status Reject Manager'),
                self::STATUS_CONFIRM_ADMIN => Yii::t('app', 'Status Confirm Admin'),
                self::STATUS_REJECT_ADMIN => Yii::t('app', 'Status Reject Admin'),
            ],
            'StatusClass' => [
                self::STATUS_WAIT_CONFIRM => 'warning',
                self::STATUS_CONFIRM_MANAGER_BRANCH => 'warning',
                self::STATUS_REJECT_MANAGER_BRANCH => 'danger',
                self::STATUS_CONFIRM_ADMIN => 'success',
                self::STATUS_REJECT_ADMIN => 'danger',
            ],
            'StatusIcon' => [
                self::STATUS_WAIT_CONFIRM => 'ph:clock-countdown-duotone',
                self::STATUS_CONFIRM_MANAGER_BRANCH => 'ph:check-circle-duotone',
                self::STATUS_REJECT_MANAGER_BRANCH => 'ph:x-circle-duotone',
                self::STATUS_CONFIRM_ADMIN => 'ph:check-circle-duotone',
                self::STATUS_REJECT_ADMIN => 'ph:x-circle-duotone'
            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
            $this->status = self::STATUS_WAIT_CONFIRM;
        }
        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
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
}
