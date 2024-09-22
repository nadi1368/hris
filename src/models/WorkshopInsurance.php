<?php

namespace hesabro\hris\models;

use backend\models\User;
use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\helpers\components\Jdf;
use common\models\Account; // TODO: What To Do
use common\models\Branch; // TODO: What To Do
use common\models\Year;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_workshop_insurance}}".
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int $branch_id
 * @property string $manager
 * @property string $additional_data
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 *
 * @property SalaryPeriod[] $salaryPeriod
 * @property Branch $branch
 * @property Account $account
 */
class WorkshopInsurance extends \yii\db\ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $error_msq = '';

    public $address, $row, $account_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_workshop_insurance}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'title', 'manager'], 'required'],
            [['address', 'row'], 'string'],
            [['address', 'row'], 'trim'],
            [['creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['code', 'manager'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 64],
            [['code'], 'unique'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['code', 'title', 'manager', 'address', 'row', 'branch_id', 'account_id'];
        $scenarios[self::SCENARIO_UPDATE] = ['code', 'title', 'manager', 'address', 'row', 'branch_id', 'account_id'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'WorkShop Code'),
            'title' => Yii::t('app', 'Title'),
            'manager' => Yii::t('app', 'Admin'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'update_id' => Yii::t('app', 'Update ID'),
            'created' => Yii::t('app', 'Created'),
            'changed' => Yii::t('app', 'Changed'),
            'address' => Yii::t('app', 'Address'),
            'branch_id' => Yii::t('app', 'Branch ID'),
            'row' => 'ردیف پیمان',
            'account_id' => 'حساب تفضیل هزینه ای حقوق و دستمزد',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalaryPeriod()
    {
        return $this->hasMany(SalaryPeriod::class, ['workshop_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'update_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::class, ['id' => 'branch_id']);
    }

    /**
     * {@inheritdoc}
     * @return WorkshopInsuranceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WorkshopInsuranceQuery(get_called_class());
    }


    public function canCreateSalary()
    {
        $startAndEndOfCurrentYear = Jdf::getStartAndEndOfCurrentYear();
        $findOpenPeriod = $this->getSalaryPeriod()
            ->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_SALARY])
            ->andWhere(['between', 'start_date', $startAndEndOfCurrentYear['start'], $startAndEndOfCurrentYear['end']])
            ->andWhere(['<>', SalaryPeriod::tableName() . '.status', SalaryPeriod::STATUS_PAYMENT])
            ->limit(1)
            ->one();
        if ($findOpenPeriod !== null) {
            $this->error_msq = "برای ایجاد دروره جدید باید وضعیت دوره  " . $findOpenPeriod->title . " در وضعیت پرداخت باشد. ";
            return false;
        }
        return true;
    }

    public function canCreateReward()
    {
        $startAndEndOfCurrentYear = Jdf::getStartAndEndOfCurrentYear();
        return $this->getSalaryPeriod()
                ->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_REWARD])
                // ->andWhere(['between', 'start_date',strtotime(Jdf::Convert_jalali_to_gregorian(Year::getDefault('start')) . ' 00:00:00'),strtotime(Year::getDefault('end') . ' 00:00:00')])
                ->andWhere(['between', 'start_date', $startAndEndOfCurrentYear['start'], $startAndEndOfCurrentYear['end']])
                ->limit(1)
                ->one() === null;
    }


    public function canCreateYear()
    {
        return $this->getSalaryPeriod()
                ->andWhere(['JSON_EXTRACT(' . SalaryPeriod::tableName() . '.`additional_data`, "$.kind")' => SalaryPeriod::KIND_YEAR])
                // ->andWhere(['between', 'start_date',strtotime(Jdf::Convert_jalali_to_gregorian(Year::getDefault('start')) . ' 00:00:00'),strtotime(Year::getDefault('end') . ' 00:00:00')])
                ->andWhere(['between', 'start_date', Year::getDefault('startTime'), Year::getDefault('endTime')])
                ->limit(1)
                ->one() === null;
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return $this->getSalaryPeriod()->one() !== null;
    }

    public function getFullName()
    {
        return $this->code . ' - ' . $this->title;
    }

    public static function itemAlias($type, $code = NULL)
    {
        $list_data = [];
        if ($type == 'List') {
            $list = self::find()->all();
            $list_data = ArrayHelper::map($list, 'id', 'fullName');
        }

        $_items = [
            'List' => $list_data,
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }


    public static function getDefault()
    {
        if (($model = self::find()->limit(1)->one()) !== null) {
            return $model->id;
        }
        return null;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
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
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'address' => 'String',
                    'row' => 'String',
                    'account_id' => 'Integer',
                ],

            ],
        ];
    }
}
