<?php

namespace hesabro\hris\models;

use common\behaviors\JsonAdditional;
use common\components\mobit\SortableGridview\SortableGridBehavior;
use common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%internal_number}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $number
 * @property array $additional_data
 *                 `['job_position' => 'NullString']`
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property User $user
 */
class InternalNumber extends ActiveRecord
{

    public $json_file;

    /**
     * Aditional data properties
     */
    public $job_position;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%internal_number}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
            [
                'class' => BlameableBehavior::class,
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'job_position' => 'NullString',
                ],
            ],
            [
                'class' => SortableGridBehavior::class,
                'sortableAttribute' => 'sort',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'number'], 'required'],
            [['job_position'], 'string'],
            [['user_id', 'sort', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['number'], 'unique'],
            [['number'], 'integer'],
            [['json_file'], 'file', 'extensions' => 'json'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sort' => Yii::t('app', 'Sort'),
            'name' => Yii::t('app', 'Name'),
            'number' => Yii::t('app', 'Number'),
            'user_id' => Yii::t('app', 'User ID'),
            'job_position' => Yii::t('app', 'Post'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
          'id',
          'name',
          'number',
          'job_position',
          'sort',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return InternalNumberQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InternalNumberQuery(get_called_class());
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->job_position && $this->job_position == "" || $this->job_position == "null") {
            $this->job_position = null;
        }

        return parent::beforeSave($insert);
    }
}
