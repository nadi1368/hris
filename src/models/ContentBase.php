<?php

namespace hesabro\hris\models;


use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\helpers\components\iconify\Iconify;
use hesabro\hris\Module;
use himiklab\sortablegrid\SortableGridBehavior;
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
class ContentBase extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    const TYPE_CUSTOMER = 1;
    const TYPE_EMPLOYEE = 2;
    const TYPE_SOFTWARE = 3;
    const TYPE_BUSINESS = 4;
    const TYPE_JOB_DESCRIPTION = 5;
    const TYPE_ANNOUNCEMENT = 6;

    const SCENARIO_CREATE = 'create';

    const SCENARIO_CREATE_ANNOUNCEMENT = 'create_announcement';

    const SCENARIO_UPDATE_ANNOUNCEMENT = 'update_announcement';

    /** Additional Data Property */
    public $clauses = [];
    public $include_client_ids;
    public $exclude_client_ids;
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
        return '{{%faq}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('master');
    }

    public function behaviors()
    {
        return [
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'notSaveNull' => true,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'clauses' => 'ClassArray::' . ContentClause::class,
                    'include_client_ids' => 'StringArray',
                    'exclude_client_ids' => 'StringArray',
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
            [['type', 'status', 'sort', 'created', 'client_id', 'creator_id', 'update_id', 'changed'], 'integer'],
            ['type', 'typeValidator'],
            ['include_client_ids', 'each', 'rule' => ['exist', 'targetClass' => Client::class, 'targetAttribute' => ['include_client_ids' => 'id']]],
            ['exclude_client_ids', 'each', 'rule' => ['exist', 'targetClass' => Client::class, 'targetAttribute' => ['exclude_client_ids' => 'id']]],
            ['custom_user_ids', 'each', 'rule' => ['exist', 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['custom_user_ids' => 'id']]],
            ['custom_job_tags', 'each', 'rule' => ['exist', 'targetClass' => Tags::class, 'targetAttribute' => ['custom_job_tags' => 'id']]],
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
            'include_client_ids' => Module::t('module', 'Clients Can See This Faq'),
            'exclude_client_ids' => Module::t('module', 'Clients Can not See This Faq'),
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

        $scenarios[self::SCENARIO_CREATE] = ['id', 'title', 'description', 'type', 'status', 'created', 'creator_id', 'update_id', 'changed', 'attachment'];
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
     * @return ContentQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new ContentQuery(get_called_class());
        return $query->active();
    }

    public function canCreate()
    {
        return true;
    }

    public function canUpdate()
    {
        if (in_array($this->type, [self::TYPE_CUSTOMER, self::TYPE_SOFTWARE])) {
            return Yii::$app->client->isMaster();
        }

        return $this->client_id == Yii::$app->client->id;
    }

    public function canDelete()
    {
        if (in_array($this->type, [self::TYPE_CUSTOMER, self::TYPE_SOFTWARE])) {
            return Yii::$app->client->isMaster();
        }

        return $this->client_id == Yii::$app->client->id;
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

    public static function itemAlias($type, $code = NULL)
    {
        $shouldIncludeAllTypes  = Yii::$app->client->isMaster() || isset($code);

        $list_data = [];
        if ($type == 'ListEmployee') {
            $list_data = ArrayHelper::map(self::find()->andWhere(['type' => self::TYPE_EMPLOYEE])->all(), 'id', 'title');
        }
        if ($type == 'ListSoftware') {
            $list_data = ArrayHelper::map(self::find()->andWhere(['type' => self::TYPE_SOFTWARE])->all(), 'id', 'title');
        }

        $_items = [
            'Status' => [
                self::STATUS_ACTIVE => Module::t('module', 'Status Active'),
                self::STATUS_DELETED => Module::t('module', 'Status Delete'),
            ],
            'Type' => array_combine([
                self::TYPE_BUSINESS,
                self::TYPE_EMPLOYEE,
                self::TYPE_JOB_DESCRIPTION,
                self::TYPE_ANNOUNCEMENT,
                ...$shouldIncludeAllTypes ? [
                    self::TYPE_CUSTOMER,
                    self::TYPE_SOFTWARE,
                ] : []
            ], [
                Module::t('module', 'Faq of Type', ['type' => Module::t('module', 'Faq Type')['business']]),
                Module::t('module', 'Faq of Type', ['type' => Module::t('module', 'Faq Type')['employee']]),
                Module::t('module', 'Faq of Type', ['type' => Module::t('module', 'Faq Type')['job_description']]),
                Module::t('module', 'Faq of Type', ['type' => Module::t('module', 'Faq Type')['announcement']]),
                ...$shouldIncludeAllTypes ? [
                    Module::t('module', 'Faq of Type', ['type' => Module::t('module', 'Faq Type')['customer']]),
                    Module::t('module', 'Faq of Type', ['type' => Module::t('module', 'Faq Type')['software']]),
                ] : []
            ]),
            'ListEmployee' => $list_data,
            'ListSoftware' => $list_data,
        ];

        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : null;
        else
            return isset($_items[$type]) ? $_items[$type] : null;
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
            $this->status = self::STATUS_ACTIVE;
        }

        $this->client_id = Yii::$app->client->id;

        if (is_array($this->clauses)) {

            // if only one clause exist and no description provided, set clause as description
            if (count($this->clauses) == 1) {
                $this->description = $this->clauses[0]->content;
                $this->clauses = [];
            } else {
                $this->description = null; // fix description unchanged on update if already set
                $this->clauses = $this->clauses;
            }
        }

        $this->custom_job_tags = $this->custom_job_tags ?: [];
        $this->custom_user_ids = $this->custom_user_ids ?: [];

        if (Yii::$app->client->isMaster()) {
            $this->include_client_ids = $this->include_client_ids && count($this->include_client_ids) > 0 ? $this->include_client_ids : ['*'];
        } else {
            $this->include_client_ids = [Yii::$app->client->id];
        }

        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
    }

    /**
     * Display included clients as list view
     *
     * @return string
     */
    public function includedClientsListView()
    {
        $result = '';

        if ($this->include_client_ids && !in_array('*', $this->include_client_ids)) {
            foreach (Client::find()->andWhere(['IN', 'id', $this->include_client_ids])->all() as $client) {
                $result .= '<label class="badge badge-info mr-1 mb-1 pull-right">' . $client->title . ' </label> ';
            }
        } else {
            $result .= '<label class="badge badge-info mr-1 mb-1 pull-right"> تمامی کلاینت ها </label> ';
        }

        return $result;
    }

    /**
     * Display excluded clients as list view
     *
     * @return string
     */
    public function excludedClientsListView()
    {
        $result = '';

        if ($this->exclude_client_ids) {
            foreach (Client::find()->andWhere(['IN', 'id', $this->exclude_client_ids])->all() as $client) {
                $result .= '<label class="badge badge-info mr-1 mb-1 pull-right">' . $client->title . ' </label> ';
            }
        }

        return $result;
    }

    public static function validateType(mixed $type): bool
    {
        return in_array((int) $type, array_keys(self::itemAlias('Type')));
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
