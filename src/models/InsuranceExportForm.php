<?php

namespace hesabro\hris\models;

use Yii;
use yii\helpers\ArrayHelper;

class InsuranceExportForm extends InternalNumberBase
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
                $this->addError($attribute, Yii::t('app', 'Your data is invalid'));
            } else {
                $this->companyIpg = $companyIpg;
            }
        }
    }
}
