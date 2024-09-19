<?php

namespace hesabro\hris\models;

use Yii;
use yii\base\Model;

class InsuranceExportForm extends Model
{
    public $DSK_KIND;
    public $DSK_LISTNO;
    public $DSK_DISC;
    public $DSK_NUM;
    public $DSK_TDD;
    public $DSK_TROOZ;
    public $DSK_TMAH;
    public $DSK_TMAZ;
    public $DSK_TMASH;
    public $DSK_TTOTL;
    public $DSK_TBIME;
    public $DSK_TKOSO;
    public $DSK_BIC;
    public $DSK_RATE;
    public $DSK_PRATE;
    public $DSK_BIMH;
    public $MON_PYM;




    public function rules()
    {
        return [
            [['DSK_KIND', 'client_secret'], 'required'],
            [['client_id'], 'validateClient'],
        ];
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

    public function attributeLabels()
    {
        return [
            'DSK_KIND' => 'نوع لیست',
            'DSK_LISTNO' => 'شماره لیست',
            'DSK_DISC' => 'شرح لیست',
            'DSK_NUM' => 'تعداد کارکنان',
            'DSK_TDD' => 'مجموع روز های کارکرد',
            'DSK_TROOZ' => 'مجموع دستمزد روزانه',
            'DSK_TMAH' => 'مجموع دستمزد ماهانه',
            'DSK_TMAZ' => 'مجموع مزایای ماهانه مشمول',
            'DSK_TMASH' => 'مجموع دستمزد مزایای ماهانه مشمول',
            'DSK_TTOTL' => 'مجموع کل مزایای  ماهانه (مشمولٍ غیر مشمول)',
            'DSK_TBIME' => 'مجموع حق بیمه کارمند',
            'DSK_TKOSO' => 'مجموع حق بیمه کارفرما',
            'DSK_BIC' => 'مجموع حق بیکاری',
            'DSK_RATE' => 'نرخ حق بیمه',
            'DSK_PRATE' => 'نرخ پورسانت',
            'DSK_BIMH' => 'نرخ مشاغل سخت و زیان',
        ];
    }

}