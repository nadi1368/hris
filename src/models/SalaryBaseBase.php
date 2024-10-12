<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%employee_salary_base}}".
 *
 * @property int $id
 * @property string $year
 * @property string $group
 * @property int $creator_id
 * @property int $update_id
 * @property int $created
 * @property int $changed
 * @property string $cost_of_year
 * @property string $cost_of_work
 * @property string $cost_of_hours
 */
class SalaryBaseBase extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_salary_base}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'group', 'cost_of_year', 'cost_of_work', 'cost_of_hours'], 'required'],
            [['creator_id', 'update_id', 'created', 'changed'], 'integer'],
            [['cost_of_year', 'cost_of_work', 'cost_of_hours'], 'number'],
            [['year'], 'string', 'max' => 4],
            [['group'], 'string', 'max' => 32],
            [['year', 'group'], 'unique', 'targetAttribute' => ['year', 'group']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'year' => 'سال',
            'group' => Module::t('module', 'Group'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'created' => Module::t('module', 'Created'),
            'changed' => Module::t('module', 'Changed'),
            'cost_of_year' => 'پایه سنوات',
            'cost_of_work' => 'مزد شغل',
            'cost_of_hours' => 'مزد یک ساعت کار عادی',
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
     * @return SalaryBaseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SalaryBaseQuery(get_called_class());
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    public static function itemAlias($type, $code = NULL, $year = null)
    {
        $list_data = [];
        if ($type == 'List') {
            $list = self::find()->andWhere(['year' => $year])->all();
            $list_data = ArrayHelper::map($list, 'id', 'fullName');
        }
        if ($type == 'CostOfYear') {
            $list = self::find()->andWhere(['year' => $year])->all();
            $list_data = ArrayHelper::map($list, 'cost_of_year', 'fullName');
        }
        $_items = [
            'List' => $list_data,
            'CostOfYear' => $list_data,
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function getFullName()
    {
        return 'گروه '.'('.$this->group.')'.' - ' . number_format((float)$this->cost_of_year);
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
