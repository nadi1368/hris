<?php

namespace hesabro\hris\models;

use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use backend\modules\master\models\Client;
use hesabro\hris\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @mixin StorageUploadBehavior
 */
class EmployeeContent extends EmployeeContentBase
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['include_client_ids', 'each', 'rule' => ['exist', 'targetClass' => Client::class, 'targetAttribute' => ['include_client_ids' => 'id']]],
            ['exclude_client_ids', 'each', 'rule' => ['exist', 'targetClass' => Client::class, 'targetAttribute' => ['exclude_client_ids' => 'id']]],
        ]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'StorageUploadBehavior' => [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_FAQ,
                'attributes' => ['attachment', 'images'],
                'scenarios' => [
                    self::SCENARIO_DEFAULT,
                    self::SCENARIO_CREATE,
                    self::SCENARIO_CREATE_ANNOUNCEMENT,
                    self::SCENARIO_UPDATE_ANNOUNCEMENT
                ],
                'accessFile' => StorageFiles::ACCESS_PUBLIC_READ,
                'deletePreviousFilesOnAttribute' => false,
                'convertImageToWebp' => true
            ]
        ]);
    }

    public function canUpdate()
    {
        return $this->slave_id == Yii::$app->client->id;
    }

    public function canDelete()
    {
        if (in_array($this->type, [self::TYPE_CUSTOMER, self::TYPE_SOFTWARE])) {
            return Yii::$app->client->isMaster();
        }

        return $this->slave_id == Yii::$app->client->id;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = time();
            $this->creator_id = Yii::$app->user->id;
            $this->status = self::STATUS_ACTIVE;
        }

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

    public static function itemAlias($type, $code = NULL)
    {
        $shouldIncludeAllTypes  = Yii::$app->client->isMaster() || isset($code);

        $list_data = [];
        if ($type == 'ListEmployee') {
            $list_data = ArrayHelper::map(self::find()->andWhere(['type' => self::TYPE_REGULATIONS])->all(), 'id', 'title');
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
                self::TYPE_REGULATIONS,
                self::TYPE_JOB_DESCRIPTION,
                self::TYPE_ANNOUNCEMENT,
                ...$shouldIncludeAllTypes ? [
                    self::TYPE_CUSTOMER,
                    self::TYPE_SOFTWARE,
                ] : []
            ], [
                Module::t('module', 'Business FAQ'),
                Module::t('module', 'Regulations'),
                Module::t('module', 'Employ Description'),
                Module::t('module', 'Notice'),
                ...$shouldIncludeAllTypes ? [
                    Module::t('module', 'Customer FAQ'),
                    Module::t('module', 'Software FAQ'),
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
}
