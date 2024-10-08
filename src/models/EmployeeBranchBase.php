<?php

namespace hesabro\hris\models;

use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\hris\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_branch}}".
 *
 * @property int $id
 * @property string $title
 * @property int $manager
 * @property int $status
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 *
 * @property object $byManager
 * @property EmployeeBranchUser[] $branchUsers
 * @property object[] $users
 */
class EmployeeBranchBase extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;


    public $user_ids;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_branch}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'manager'], 'required'],
            [['user_ids'], 'safe'],
            [['manager', 'status', 'creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['manager'], 'exist', 'skipOnError' => true, 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['manager' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'manager' => Module::t('module', 'Pay Admin'),
            'status' => Module::t('module', 'Status'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'created' => Module::t('module', 'Created'),
            'changed' => Module::t('module', 'Changed'),
            'user_ids' => Module::t('module', 'Employee User Ids'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getByManager()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'manager']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranchUsers()
    {
        return $this->hasMany(EmployeeBranchUser::class, ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Module::getInstance()->user, ['id' => 'user_id'])->viaTable('{{%employee_branch_user}}', ['branch_id' => 'id']);
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
     * @return EmployeeBranchQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new EmployeeBranchQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return !$this->getUsers()->count();
    }

    /*
    * حذف منطقی
    */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        if ($this->canDelete() && $this->save()) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * فعال کردن
    */
    public function restore()
    {
        $this->status = self::STATUS_ACTIVE;
        if ($this->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function showUsersList()
    {
        $branches = '';
        foreach ($this->getBranchUsers()->all() as $user) {
            $branches .= '<label class="badge badge-info mr-2 mb-2">' . $user->user->fullName . ' </label> ';
        }
        return $branches;
    }

    public function createUser()
    {
        $this->user_ids = is_array($this->user_ids) ? $this->user_ids : [];
        foreach ($this->user_ids as $user_id) {
            $branch_user = new EmployeeBranchUser();
            $branch_user->branch_id = $this->id;
            $branch_user->user_id = $user_id;
            if (!$flag = $branch_user->save()) {
                $this->addError('user_ids', $branch_user->getFirstError('user_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @param $old_user_ids
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function updateUser($old_user_ids)
    {
        $this->user_ids = is_array($this->user_ids) ? $this->user_ids : [];
        $deleted_user_ids = array_diff($old_user_ids, $this->user_ids);
        $insert_user_ids = array_diff($this->user_ids, $old_user_ids);
        $flag = true;
        if (!empty($deleted_user_ids)) {

            foreach ($deleted_user_ids as $user_id) {
                if(SalaryPeriodItems::find()->andWhere(['user_id'=>$user_id])->limit(1)->one()!==null)
                {
                    $this->addError('user_ids', 'امکان حذف کارمند از شعبه وجود ندارد.فقط میتوانید شعبه آن را از بخش کارمندان دپارتمان تغیر دهید.');
                    return false;
                }elseif (($model = EmployeeBranchUser::find()->andWhere(['user_id' => $user_id, 'branch_id' => $this->id])->one()) !== null && !$model->deleteWithLog()) {
                    $this->addError('user_ids', 'خطا در حذف کارمند از شعبه.');
                    return false;
                }
            }
        }

        foreach ($insert_user_ids as $user_id) {
            $branch_user = new EmployeeBranchUser();
            $branch_user->branch_id = $this->id;
            $branch_user->user_id = $user_id;
            if (!$flag = $branch_user->save()) {
                $this->addError('user_ids', $branch_user->getFirstError('user_id'));
                return false;
            }
        }
        return $flag;
    }

    public static function itemAlias($type, $code = NULL)
    {
        $list_data = [];
        if ($type == 'List') {
            $list = self::find()->all();
            $list_data = ArrayHelper::map($list, 'id', 'title');
        }

        $_items = [
            'List' => $list_data
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
            $this->status = self::STATUS_ACTIVE;
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
                'saveAfterInsert' => true,
                'excludeAttribute' => ['changed', 'update_id'],
            ],
        ];
    }

}
