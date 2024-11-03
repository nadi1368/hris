<?php

namespace hesabro\hris\controllers;

use common\models\UserUpload;
use hesabro\hris\Module;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    protected function findModelUserUpload($id)
    {
        if (($model = UserUpload::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', "The requested page does not exist."));
    }
}