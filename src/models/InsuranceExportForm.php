<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use Yii;
use yii\helpers\ArrayHelper;

class InsuranceExportForm extends InsuranceExportFormBase
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['client_id'], 'validateClient']
        ]);
    }

    public function validateClient($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $companyIpg = CompanyIpg::find()->joinWith(['company'])->andWhere(['client_id' => $this->client_id])->one();
            if (
                $companyIpg === null ||
                !Yii::$app->security->validatePassword($this->client_secret, $companyIpg->client_secret) ||
                (!YII_DEBUG && !ArrayHelper::isIn(Yii::$app->request->userIP, $companyIpg->trustedIps))
            ) {
                Yii::error('Ip is invalid', 'Exception/CompanyLogin');
                $this->addError($attribute, Module::t('module', 'Your data is invalid'));
            } else {
                $this->companyIpg = $companyIpg;
            }
        }
    }
}
