<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_salary_insurance}}".
 *
 * @property int $id
 * @property string $code
 * @property string $group
 * @property int $tag_id
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property-read string $fullName
 * @property-read \yii\db\ActiveQuery $update
 * @property-read \yii\db\ActiveQuery $creator
 * @property int $changed
 */
class SalaryInsuranceBase extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_salary_insurance}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'group'], 'required'],
            [['creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['code'], 'number'],
            [['group'], 'string', 'max' => 64],
            [['code'], 'unique']
        ];
    }

    public function behaviors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'code' => Module::t('module', 'Job Code'),
            'group' => Module::t('module', 'Title'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'created' => Module::t('module', 'Created'),
            'changed' => Module::t('module', 'Changed'),
            'tag_id' => Module::t('module', 'Job')
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
     * {@inheritdoc}
     * @return SalaryInsuranceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SalaryInsuranceQuery(get_called_class());
    }

    public function canUpdate()
    {
        if(!EmployeeBranchUser::find()->byJobCode($this->id)->exists() || Yii::$app->user->can('superadmin'))
        {
            return true;
        }
        return false;
    }

    public function canDelete()
    {
        if(EmployeeBranchUser::find()->byJobCode($this->id)->exists())
        {
            return false;
        }
        return true;
    }

    public function getFullName()
    {
        return $this->code.' - '.$this->group;
    }

    public static function itemAlias($type, $code = NULL)
    {
        $list_data = [];
        if ($type == 'List') {
            $list = self::find()->all();
            $list_data = ArrayHelper::map($list, 'id', 'fullName');
        }

        $_items = [
            'List'=>$list_data
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
        }
        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
    }
}
