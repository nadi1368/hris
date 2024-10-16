<?php

namespace hesabro\hris\models;

use common\interfaces\SendAutoCommentInterface;
use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\SendAutoCommentsBehavior;
use common\models\Account;
use common\models\BalanceDetailed;
use common\models\CommentsType;
use common\models\Customer;
use common\models\Document;
use common\models\Settings;
use common\models\Year;
use hesabro\hris\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @mixin StorageUploadBehavior
 */
class EmployeeBranchUser extends EmployeeBranchUserBase implements SendAutoCommentInterface
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['end_work'], 'validateEndWork', 'skipOnError' => false, 'skipOnEmpty' => false, 'on' => [self::SCENARIO_SET_END_WORK]],
        ]);
    }

    public function validateEndWork($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->end_work < $this->start_work || $this->end_work > Yii::$app->jdf::jdate("Y/m/d")) {
                $this->addError($attribute, Module::t('module', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
            } elseif (($year = Year::find()->byDate($this->end_work)->one()) === null || !$year->isSetSettingForYearPeriod()) {
                $this->addError($attribute, 'برای سال مورد نظر تنظیمات اولیه حقوق و دستمزد ست نشده است');
            } elseif (($lastPayment = SalaryPeriodItems::find()->byUser($this->user_id)->joinWith(['period'])->limit(1)->andWhere(['>', 'basic_salary', 0])->orderBy(['start_date' => SORT_DESC])->one()) !== null && $this->end_work != Yii::$app->jdf::jdate("Y/m/d", $lastPayment->period->end_date)) {
                $this->addError($attribute, "تاریخ ترک کار باید برابر با " . Yii::$app->jdf::jdate("Y/m/d", $lastPayment->period->end_date) . " باشد.");
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * @return array
     * get list of insurance data
     */
    public function getInsuranceData($for_contract = false)
    {
        $data = array_merge(parent::getInsuranceData($for_contract), [
            'national' => is_array(Customer::itemAlias('National', $this->national)) ? null : Customer::itemAlias('National', $this->national),
            'sex' => is_array(Customer::itemAlias('SexTitle', $this->sex)) ? null : Customer::itemAlias('SexTitle', $this->sex),
        ]);

        if ($for_contract) {
            $data += [
                'company_name' => Module::getInstance()->settings::get('business_name', true),
                'company_ceo' => Module::getInstance()->settings::get('employee_company_ceo', true),
                'company_number' => Module::getInstance()->settings::get('business_phone_number', true),
                'company_address' => Module::getInstance()->settings::get('business_address', true),
                'company_national_code' => Module::getInstance()->settings::get('national_id', true),
            ];
        }

        return $data;
    }

    // Documents

    public function saveDocumentEndWork()
    {
        $document = new Document();
        $document->type = Document::TYPE_CHECKOUT_EMPLOYEE;
        $document->is_auto = 1;
        $document->model_id = $this->user_id;
        $document->h_date = $this->end_work;
        $document->des = 'تسویه حساب کارمند ' . $this->user->fullName;
        $flag = $document->save();

        $year = Year::find()->byDate($this->end_work)->one();
        /****************** محاسبه سنوات ******************/
        $modelSalaryPeriodItemsYear = new SalaryPeriodItems(['user_id' => $this->user_id, 'yearModel' => $year]);
        $modelSalaryPeriodItemsYear->loadDefaultValuesBeforeCreateYear(strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($this->end_work) . ' 23:59:59'), $year);
        $debtor = BalanceDetailed::getBalance(Module::getInstance()->settings::get('year_period_m_id', true), $this->account_id, true);

        if (($yearPayment = $modelSalaryPeriodItemsYear->payment_salary - $debtor) > 0) {
            /****************** بدهکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('year_period_interface', true), null, $yearPayment, 0, $document->des . ' - سنوات ' . $modelSalaryPeriodItemsYear->hours_of_work . ' روز');
            /****************** بستانکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('year_period_m_id', true), $this->account_id, 0, $yearPayment, $document->des . ' - سنوات ' . $modelSalaryPeriodItemsYear->hours_of_work . ' روز');
        }

        /****************** محاسبه عیدی پاداش ******************/
        $modelSalaryPeriodItemsReward = new SalaryPeriodItems(['user_id' => $this->user_id, 'yearModel' => $year]);
        $modelSalaryPeriodItemsReward->loadDefaultValuesBeforeCreateReward(strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($this->end_work) . ' 23:59:59'), $year);


        $modelSalaryPeriodItemsReward->tax = $modelSalaryPeriodItemsReward->calculateTaxReward($modelSalaryPeriodItemsReward->getTotalInYear() + $modelSalaryPeriodItemsReward->total_salary - ($year->COST_TAX_STEP_1_MIN) - ((int)$modelSalaryPeriodItemsReward->getTotalInYear('insurance') * 2 / 7));
        $modelSalaryPeriodItemsReward->payment_salary = (int)($modelSalaryPeriodItemsReward->total_salary - $modelSalaryPeriodItemsReward->tax);
        /****************** بدهکار ******************/
        if ($modelSalaryPeriodItemsReward->total_salary > 0) {
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_m_id', true), null, $modelSalaryPeriodItemsReward->total_salary, 0, $document->des . ' - عیدی پاداش ');
        }

        /****************** بستانکار ******************/
        if ($modelSalaryPeriodItemsReward->tax > 0) {
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_tax_m_id', true), Module::getInstance()->settings::get('salary_period_tax_t_id', true), 0, $modelSalaryPeriodItemsReward->tax, $document->des . ' - عیدی پاداش ');
        }

        if ($modelSalaryPeriodItemsReward->payment_salary > 0) {
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $this->account_id, 0, $modelSalaryPeriodItemsReward->payment_salary, $document->des . ' - عیدی پاداش ');
        }

        /****************** کسر مساعده ******************/
        if ($modelSalaryPeriodItemsReward->advance_money > 0) {
            /****************** بدهکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $this->account_id, $modelSalaryPeriodItemsReward->advance_money, 0, $document->des . ' - کسر مساعده');
            /****************** بستانکار ******************/
            $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_debtor_advance_money', true), $this->account_id, 0, $modelSalaryPeriodItemsReward->advance_money, $document->des . ' - کسر مساعده');

        }
        return $flag && $document->validateTaraz();
    }


    /**
     * @return Document|null
     */
    public function getDocumentEndWork()
    {
        return Document::find()->byModel($this->user_id, Document::TYPE_CHECKOUT_EMPLOYEE)->limit(1)->orderBy(['h_date' => SORT_DESC])->one();
    }

    /**
     * @return bool
     */
    public function deleteDocumentEndWork()
    {
        $document = Document::find()->findByModel($this->user_id)->findByType(Document::TYPE_CHECKOUT_EMPLOYEE)->one();
        if ($document !== null) {
            if (!$document->canDelete(false, false)) {
                //$this->error_msq = 'امکان حذف سند حسابداری وجود ندارد.';
                return false;
            }
            if (!$document->delete()) {
                //$this->error_msq = 'امکان حذف سند حسابداری وجود ندارد.';
                return false;
            }
        }
        return true;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::EMPLOYEE_UPDATE_PROFILE,
                'title' => 'درخواست ویرایش حساب کاربری',
                'scenarioValid' => [self::SCENARIO_UPDATE_PROFILE],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::EMPLOYEE_UPDATE_PROFILE_REJECT,
                'title' => 'رد درخواست ویرایش حساب کاربری',
                'scenarioValid' => [self::SCENARIO_REJECT_UPDATE],
                'callAfterUpdate' => true
            ],
            'StorageUploadBehavior' => [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_EMPLOYEE_BRANCH_USER,
                'attributes' => [
                    'sh_picture_first', 'sh_picture_second', 'sh_picture_third',
                    'id_card_front', 'id_card_back', 'resume_file', 'military_doc',
                    'education_picture', 'insurance_history'
                ],
                'scenarios' => [self::SCENARIO_UPDATE_PROFILE, self::SCENARIO_INSURANCE],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'primaryKey' => 'user_id',
            ],
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (
            $this->scenario === self::SCENARIO_INSURANCE &&
            $this->job_code &&
            $salaryInsurance = SalaryInsurance::findOne($this->job_code)
        ) {
            if ($salaryInsurance->tag_id && $customer = Customer::find()->findByUser($this->user_id)->one()) {
                $customer->jobs = [$salaryInsurance->tag_id];
                $customer->save(false);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function getPendingDataHint(string $attribute, bool $mainValue = false, string $default = null): array
    {
        if ($this->hasPendingData($attribute)) {
            $value = ArrayHelper::getValue($mainValue ? array_merge($this->attributes, $this->additional_data ?: []) : $this->pending_data, $attribute);
            $attr = explode('.', $attribute);
            $value = match (end($attr)) {
                'marital' => self::itemAlias('marital', $value),
                'education' => self::itemAlias('education', $value),
                'sex' => Module::getInstance()->user::itemAlias('Sex', $value),
                'national' => Customer::itemAlias('National', $value),
                'insurance' => EmployeeChild::itemAlias('insurance', $value),
                default => $value
            };

            if (!is_array($value)) {
                return [
                    $mainValue ?
                        Module::t('module', 'Old Value',) . ": $value" :
                        Module::t('module', 'Pending Value') . ": $value",
                    ['class' => 'profile-input-hint']
                ];
            }
        }

        return [
            !is_null($default) ? $default : $this->getAttributeHint($attribute),
            ['class' => 'text-muted']
        ];
    }
}
