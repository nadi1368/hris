<?php
namespace hesabro\hris\models;

use Yii;
use yii\base\Model;

class RejectForm extends Model
{
    public $description;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['description', 'trim'],
            ['description', 'required'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'description' => Yii::t('app','Description'),
        ];
    }


}
