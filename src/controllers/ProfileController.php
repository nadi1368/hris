<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeChild;
use hesabro\hris\models\EmployeeExperience;
use hesabro\helpers\traits\AjaxValidationTrait;
use hesabro\hris\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ProfileController extends Controller
{
    use AjaxValidationTrait;

    public $layout = 'panel';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['@']
                        ]
                    ]
            ]
        ];
    }
}
