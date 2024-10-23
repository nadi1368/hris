<?php

namespace hesabro\hris\models;

use hesabro\helpers\traits\ModelHelper;
use Ramsey\Uuid\Uuid;
use yii\base\Model;

class EmployeeContentClause extends Model
{
    use ModelHelper;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $content;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'content'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        if (!$this->id) $this->id = (string) Uuid::uuid4();
    }
}
