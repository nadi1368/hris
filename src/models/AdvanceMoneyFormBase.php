<?php

namespace hesabro\hris\models;

use hesabro\errorlog\behaviors\TraceBehavior;
use Yii;
use yii\base\Model;

/**
 * Class AdvanceMoneyForm
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AdvanceMoneyFormBase extends Model
{
    public ?int $definite_id_from = null;
    public  $account_id_from = null;
    public ?int $user_id = null;
    public ?int $account_id_to = null; // حساب تفضیل حقوق کاربر

    public ?int $amount = null;
    public ?string $description = null;
    public ?string $date = null;
    public $btn_type;
    /** @var EmployeeBranchUser $employee */
    public $employee;

    public $document_id;
    public $error_msg = '';

    public function attributeLabels()
    {
        return [
            'definite_id_from' => Yii::t('app', 'M Id Creditor'),
            'account_id_from' => Yii::t('app', 'T Id Creditor'),
            'account_id_to' => 'تفضیل کارمند (بدهکار)',
            'amount' => Yii::t('app', 'Amount'),
            'date' => Yii::t('app', 'Date'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return bool
     */
    public function canCreate(): bool
    {
        if ($this->account_id_to === null || $this->accountTo === null) {
            $this->error_msg = 'حساب تفضیل کارمند ست نشده است';
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function saveAdvanceMoney(): bool
    {
        $model = new AdvanceMoney(['scenario' => AdvanceMoney::SCENARIO_CREATE_WITH_CONFIRM]);
        $model->user_id = $this->user_id;
        $model->amount = $this->amount;
        $model->comment = $this->description;
        $model->created = Yii::$app->jdf::jalaliToTimestamp($this->date);
        $model->changed = Yii::$app->jdf::jalaliToTimestamp($this->date);
        $model->creator_id = Yii::$app->user->id;
        $model->update_id = Yii::$app->user->id;
        $model->status = AdvanceMoney::STATUS_CONFIRM;
        $model->doc_id = $this->document_id;
        return $model->save();
    }

    public function behaviors()
    {
        return [
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
        ];
    }
}
