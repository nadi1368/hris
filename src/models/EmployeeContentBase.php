<?php

namespace hesabro\hris\models;


use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\helpers\components\iconify\Iconify;
use hesabro\hris\Module;
use himiklab\sortablegrid\SortableGridBehavior;
use mamadali\S3Storage\behaviors\StorageUploadBehavior;
use mamadali\S3Storage\components\S3Storage;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%faq}}".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $type
 * @property int $status
 * @property int $created
 * @property int $creator_id
 * @property int $update_id
 * @property int $changed
 * @property int $sort
 * @property array $additional_data
 */
class EmployeeContentBase extends ActiveRecord
{
    const STATUS_ACTIVE = 1;

    const STATUS_DELETED = 0;

    const TYPE_CUSTOMER = 1;

    const TYPE_REGULATIONS = 2;

    const TYPE_SOFTWARE = 3;

    const TYPE_BUSINESS = 4;

    const TYPE_JOB_DESCRIPTION = 5;

    const TYPE_ANNOUNCEMENT = 6;


    const SCENARIO_CREATE = 'create';

    const SCENARIO_CREATE_ANNOUNCEMENT = 'create_announcement';

    const SCENARIO_UPDATE_ANNOUNCEMENT = 'update_announcement';

    /** Additional Data Property */
    public $clauses = [];
    public $custom_user_ids = [];
    public $custom_job_tags = [];

    public mixed $show_start_at = null;

    public mixed $show_end_at = null;

    public mixed $attachment = null;

    /**
     * Uploaded images in ckeditor
     */
    public mixed $images = null;

    /**
     * Define templateReplacement for the model
     *
     * @var string|bool
     */
    public static $templateReplacement = true;

    /**
     * Scattered search query
     * used in in guide searches(\backend\controllers\GuideController)
     *
     * @var string|null
     */
    public string|null $scattered_search_query = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_content}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => LogBehavior::class,
                'ownerClassName' => 'backend\modules\employee\models\EmployeeContent',
                'saveAfterInsert' => true,
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'notSaveNull' => true,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'clauses' => 'ClassArray::' . EmployeeContentClause::class,
                    'custom_user_ids' => 'StringArray',
                    'custom_job_tags' => 'StringArray',
                    'show_start_at' => 'Integer',
                    'show_end_at' => 'Integer'
                ],
            ],
            [
                'class' => SortableGridBehavior::class,
                'sortableAttribute' => 'sort',
            ],
            'StorageUploadBehavior' => [
                'class' => StorageUploadBehavior::class,
                'attributes' => ['attachment', 'images'],
                'accessFile' => S3Storage::ACCESS_PRIVATE,
                'scenarios' => [
                    self::SCENARIO_DEFAULT,
                    self::SCENARIO_CREATE,
                    self::SCENARIO_CREATE_ANNOUNCEMENT,
                    self::SCENARIO_UPDATE_ANNOUNCEMENT
                ],
                'path' => 'hris/employee-content/{id}',
            ],
        ];
    }

    public function beforeValidate()
    {
        $this->show_start_at = $this->show_start_at ? Yii::$app->jdf::jalaliToTimestamp("$this->show_start_at 00:00:00") : null;
        $this->show_end_at = $this->show_end_at ? Yii::$app->jdf::jalaliToTimestamp("$this->show_end_at 23:59:59") : null;

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'title'], 'required'],
            [['title', 'description', 'scattered_search_query'], 'string'],
            [['type', 'status', 'sort', 'created', 'creator_id', 'update_id', 'changed'], 'integer'],
            ['type', 'typeValidator'],
            ['custom_user_ids', 'each', 'rule' => ['exist', 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['custom_user_ids' => 'id']]],
            ['custom_job_tags', 'each', 'rule' => ['exist', 'targetClass' => SalaryInsurance::class, 'targetAttribute' => ['custom_job_tags' => 'id']]],
            ['clauses', 'safe'],
            [
                ['show_start_at', 'show_end_at'],
                'integer',
                'enableClientValidation' => false
            ],
            [
                ['show_start_at', 'show_end_at'],
                'integer',
                'enableClientValidation' => false
            ],
            [
                'attachment',
                'file',
                'extensions' => [
                    'webp', 'jpg', 'jpeg', 'png', 'docx', 'doc', 'xlsx', 'xlsm', 'zip', 'pdf', 'csv'
                ]
            ],
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
            'description' => Module::t('module', 'Description'),
            'type' => Module::t('module', 'Type'),
            'status' => Module::t('module', 'Status'),
            'sort' => Module::t('module', 'Sort'),
            'created' => Module::t('module', 'Created'),
            'creator_id' => Module::t('module', 'Creator ID'),
            'update_id' => Module::t('module', 'Update ID'),
            'changed' => Module::t('module', 'Creator'),
            'clauses' => Module::t('module', 'Clauses'),
            'custom_job_tags' => Module::t('module', 'Job'),
            'custom_user_ids' => Module::t('module', 'Custom Users'),
            'show_start_at' => Module::t('module', 'Show Banner From Date'),
            'show_end_at' => Module::t('module', 'Show Banner Until Date'),
            'attachment' => Module::t('module', 'Attachment'),
        ];
    }

    /**
     * Validate type
     */
    public function typeValidator($attribute, $params, $validator)
    {
        if (!self::validateType($this->$attribute)) {
            $this->addError($attribute, Module::t('module', 'Invalid type'));
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = [
            'id', 'title', 'description', 'type', 'status', 'created', 'creator_id', 'update_id', 'changed', 'attachment',
            'show_start_at', 'show_end_at', 'custom_job_tags', 'custom_user_ids'
        ];
        $scenarios[self::SCENARIO_CREATE_ANNOUNCEMENT] = [
            'id', 'title', 'description', 'type', 'status', 'created', 'creator_id', 'update_id', 'changed',
            'show_start_at', 'show_end_at', 'attachment'
        ];
        $scenarios[self::SCENARIO_UPDATE_ANNOUNCEMENT] = [
            'id', 'title', 'description', 'type', 'status', 'created', 'creator_id', 'update_id', 'changed',
            'show_start_at', 'show_end_at', 'attachment'
        ];

        return $scenarios;
    }

    /**
     * @return string
     */
    public function getContent($highlightedClauseId = null)
    {
        $content = implode('<br/>', array_merge(
                [$this->description],
                array_map(fn($clause) => '<div id="clause-' . $clause->id . '" class="' . ($highlightedClauseId == $clause->id ? 'highlight' : '') . '">' . $clause->content . '</div>', $this->clauses ?? [])
            )
        );
        return $content;
    }

    /**
     * @return string
     */
    public function getExcerpt()
    {
        return str_replace("&nbsp;", "", strip_tags($this->getContent()));
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
     * @return EmployeeContentQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new EmployeeContentQuery(get_called_class());
        return $query->active();
    }

    public function canCreate()
    {
        return true;
    }

    /*
    * حذف منطقی
    */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        if ($this->canDelete() && $this->save(false)) {
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

    public static function validateType(mixed $type): bool
    {
        return in_array((int) $type, array_keys(EmployeeContent::itemAlias('Type')));
    }

    /**
     * Define template variables to replace after find
     *
     * @return array
     */
    public function templateVariables(): array
    {
        return [
            'base_url' => Yii::$app->urlManager->createAbsoluteUrl(''),
            'iconify' => fn(...$params) => Iconify::getInstance()->icon(...$params)
        ];
    }

    /**
     * Repalce template variables
     *
     * @param string|null string to convert
     * @return string|null replaced result
     */
    private function replaceTemplateVariables(string|null $content): string|null
    {
        if(self::$templateReplacement || (is_string(self::$templateReplacement) && self::$templateReplacement == '')) {
            $variables = $this->templateVariables();

            return $content ? preg_replace_callback('/{\s*(.*?)\s*}/', function($matches) use ($variables) {
                $expression = trim($matches[1]);

                if (preg_match('/(\w+)\((.*?)\)/', $expression, $functionMatches)) {
                    $functionName = $functionMatches[1];
                    $paramsString = $functionMatches[2];

                    $params = array_map('trim', explode(',', $paramsString));

                    if (isset($variables[$functionName]) && is_callable($variables[$functionName])) {
                        return call_user_func_array($variables[$functionName], $params);
                    }
                }

                return is_string(self::$templateReplacement) ? self::$templateReplacement : ($variables[$expression] ?? $matches[0]);
            }, $content) : $content;
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->description = $this->replaceTemplateVariables($this->description);
        foreach($this->clauses ?? [] as $clause) {
            $clause->content = $this->replaceTemplateVariables($clause->content);
        }
    }


    /**
     * Check if faq contains specific clause id
     *
     * @param string|null clauseId
     * @return bool
     */
    public function containsClauseId(string|null $clauseId): bool
    {
        if ($this->clauses) {
            return count(array_filter($this->clauses, fn($clause) => $clause->id == $clauseId));
        }
        return false;
    }


    /**
     * Get substring clause id
     *
     * @param string|null substring to search
     * @return string|null clause id if exist, null otherwise
     */
    public function containedClauseId(string|null $query): string|null
    {
        if(!$query || !$this->clauses || !is_array($this->clauses))
            return null;

        foreach ($this->clauses as $clause) {
            if (isset($clause->content) && mb_strpos($clause->content, $query) !== false) {
                return $clause->id;
            }
        }

        return null;
    }
}
