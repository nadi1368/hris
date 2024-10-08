<?php

namespace hesabro\hris\models;

use hesabro\helpers\traits\ModelHelper;
use Yii;
use yii\base\Model;

class ContractClausesModel extends Model
{
    use ModelHelper;

	public ?string $title = null;
	public ?string $description = null;

	//public $variables;

	public function rules()
	{
		return [
			[['title', 'description'], 'required'],
			[['title', 'description'], 'string'],
			[['variables'], 'safe'],
            //[['variables'], 'validateVariables'],
		];
	}

    public function attributeLabels()
	{
		return [
			'title' => Yii::t('app','Title'),
			'description' => Yii::t('app','Description'),
		];
	}

//    public function validateVariables()
//    {
//        if (!empty($this->variables)) {
//            foreach ($this->variables as $key => $variable) {
//                if (!$variable) {
//                    $this->addError("variables[$key]", Yii::t('app', 'Variable name is required'));
//                }
//            }
//        }
//    }
}
