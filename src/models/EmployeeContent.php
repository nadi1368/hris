<?php

namespace hesabro\hris\models;

use backend\modules\master\models\Client;
use mamadali\S3Storage\behaviors\StorageUploadBehavior;
use mamadali\S3Storage\components\S3Storage;
use hesabro\hris\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @mixin StorageUploadBehavior
 */
class EmployeeContent extends EmployeeContentBase
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
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

        $this->update_id = Yii::$app->user->id;
        $this->changed = time();
        return parent::beforeSave($insert);
    }

    public static function itemAlias($type, $code = NULL)
    {
        $shouldIncludeAllTypes  = Yii::$app->client->isMaster() || isset($code);

        $list_data = [];
        if ($type == 'ListRegulation') {
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
            'ListRegulation' => $list_data,
            'ListSoftware' => $list_data,
        ];

        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : null;
        else
            return isset($_items[$type]) ? $_items[$type] : null;
    }
}
