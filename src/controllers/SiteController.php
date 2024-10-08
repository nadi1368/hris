<?php

namespace hesabro\hris\controllers;

use common\models\UserUpload;
use Yii;
use yii\web\NotFoundHttpException;

class SiteController extends SiteBase
{
    /**
     * @throws NotFoundHttpException
     */
    protected function findModelUserUpload($id)
    {
        if (($model = UserUpload::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
    }
}