<?php

namespace hesabro\hris\models;

use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;

/**
 * @mixin StorageUploadBehavior
 */
class Content extends ContentBase
{
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
}
