<?php
namespace hesabro\hris\models;

use hesabro\hris\Module;
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
            'description' => Module::t('module','Description'),
        ];
    }


}
