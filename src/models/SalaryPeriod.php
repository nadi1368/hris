<?php

namespace hesabro\hris\models;

use hesabro\changelog\behaviors\LogBehavior;
use hesabro\helpers\behaviors\DocumentsDataBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\hris\Module;
use Yii;
use hesabro\helpers\components\Helper;
use common\models\BalanceDetailed;
use common\models\Document;
use common\models\Settings;
use common\models\UserPoints;
use common\models\Year;
use common\models\Mutex;
use common\behaviors\MutexBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class SalaryPeriod
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 *
 * @property array createDocumentNonCashLink
 * @mixin MutexBehavior
 */
class SalaryPeriod extends SalaryPeriodBase
{
    const DOCUMENT_TYPE_SALARY_PERIOD = Document::TYPE_SALARY_PERIOD;
    const DOCUMENT_TYPE_SALARY_PERIOD_ADVANCE_MONEY = Document::TYPE_SALARY_PERIOD_ADVANCE_MONEY;
    const DOCUMENT_TYPE_SALARY_PERIOD_NON_CASH_PAYMENT = Document::TYPE_SALARY_PERIOD_NON_CASH_PAYMENT;
    const DOCUMENT_TYPE_SALARY_INSURANCE_ADDITION = Document::TYPE_SALARY_INSURANCE_ADDITION;
    const DOCUMENT_TYPE_SALARY_PERIOD_PAYMENT = Document::TYPE_SALARY_PERIOD_PAYMENT;

    const DOCUMENT_TYPE_YEAR_PERIOD_CLEARING = Document::TYPE_YEAR_PERIOD_CLEARING;

    const DOCUMENT_TYPE_YEAR_PERIOD = Document::TYPE_YEAR_PERIOD;

    const MutexSalaryPeriodConfirm = Mutex::SalaryPeriodConfirm;
    const MutexSalaryPeriodPayment = Mutex::SalaryPeriodPayment;

    /**
     * @return array|Year|null
     */
    public function getYear()
    {
        return Year::find()->byDate(Yii::$app->jdf->jdate("Y/m/d", $this->start_date))->one();
    }

    /**
     * @return bool
     */
    public function saveDocumentConfirm()
    {
        $document = new Document();
        $document->type = Document::TYPE_SALARY_PERIOD;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
        $document->des = "شناسایی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
        $flag = $document->save();

        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_insurance_m_id', true), Module::getInstance()->settings::get('salary_period_insurance_t_id', true), 0, $this->getSalaryPeriodItems()->sum('insurance+insurance_owner'), $document->des, $document->h_date); // سازمان تامین اجتماعی
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_tax_m_id', true), Module::getInstance()->settings::get('salary_period_tax_t_id', true), 0, $this->getSalaryPeriodItems()->sum('tax'), $document->des, $document->h_date); // سازمان امور مالیاتی


        $total = [];
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            $branchId = $item->employee->branch_id;
            if (!isset($total[$branchId])) {
                $total[$branchId] = ['salary' => 0, 'insurance_owner' => 0];
            }
            $total[$branchId]['salary'] += $item->total_salary;
            $total[$branchId]['insurance_owner'] += $item->insurance_owner;
            if ($item->payment_salary > 0) {
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id, 0, $item->payment_salary, $document->des, $document->h_date); // حقوق ودستمزد پرداختنی
            }
        }

        /****************** بدهکار ******************/
        foreach ($total as $branchId => $value) {
            $branch = EmployeeBranch::findOne($branchId);
            if($branch->canSaveDocument())
            {
                $flag = $flag && $document->saveDetailWitDefinite($branch->definite_id_salary, ($branch->account_id_salary ?: null), $value['salary'], 0, $document->des.' - '.$branch->title, $document->h_date); // حقوق و دستمزد
                $flag = $flag && $document->saveDetailWitDefinite($branch->definite_id_insurance_owner, ($branch->account_id_insurance_owner ?: null), $value['insurance_owner'], 0, $document->des.' - '.$branch->title, $document->h_date); // بیمه سهم کارفرما
            }else{
                $this->addError('title',"برای دپارتمان ".$branch->title." حساب های حقوق و دستمزد تنظیم نشده است.");
                $this->afterValidate();
                return false;
            }
        }
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند شناسایی حقوق');
    }

    /**
     * @return bool
     */
    public function saveDocumentConfirmReward()
    {
        $document = new Document();
        $document->type = Document::TYPE_SALARY_PERIOD;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
        $document->des = "شناسایی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
        $flag = $document->save();

        /****************** بدهکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_m_id', true), null, $this->getSalaryPeriodItems()->sum('total_salary'), 0, $document->des, $document->h_date); // حقوق و دستمزد


        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_tax_m_id', true), Module::getInstance()->settings::get('salary_period_tax_t_id', true), 0, $this->getSalaryPeriodItems()->sum('tax'), $document->des, $document->h_date); // سازمان امور مالیاتی
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            if ($item->payment_salary > 0) {
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $item->employee->account_id, 0, $item->payment_salary, $document->des, $document->h_date); // حقوق ودستمزد پرداختنی
            }
        }
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند شناسایی حقوق');
    }

    /**
     * @return bool
     * سند تهاتر حساب سنوات
     */
    public function saveDocumentInterfaceYear()
    {
        $totalDebtor = 0;
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item */
            $debtor = BalanceDetailed::getBalance(Module::getInstance()->settings::get('year_period_m_id', true), $item->employee->account_id, true);
            if ($debtor > 0) {
                $totalDebtor += $debtor;
            }
        }
        if ($totalDebtor == 0) {
            return true;
        }
        $document = new Document();
        $document->type = Document::TYPE_YEAR_PERIOD_CLEARING;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
        $document->des = $this->title;
        $flag = $document->save();

        $totalDebtor = 0;
        /****************** بدهکار ******************/
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item */
            $debtor = BalanceDetailed::getBalance(Module::getInstance()->settings::get('year_period_m_id', true), $item->employee->account_id, true);
            if ($debtor > 0) {
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('year_period_m_id', true), $item->employee->account_id, $debtor, 0, $document->des, $document->h_date); //  سنوات پایان خدمت پرداختنی / تفصیلی پذیر
                $totalDebtor += $debtor;
            }
        }
        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('year_period_interface', true), null, 0, $totalDebtor, $document->des, $document->h_date); // هزینه مزد سنوات پرسنل
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند تهاتر سنوات');
    }

    /**
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     * تایید سنوات
     */
    public function saveDocumentConfirmYear()
    {
        $document = new Document();
        $document->type = Document::TYPE_YEAR_PERIOD;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
        $document->des = $this->title;
        $flag = $document->save();

        /****************** بدهکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('year_period_interface', true), null, $this->getSalaryPeriodItems()->sum('total_salary'), 0, $document->des, $document->h_date);

        /****************** بستانکار ******************/
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            if ($item->payment_salary > 0) {
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('year_period_m_id', true), $item->employee->account_id, 0, $item->payment_salary, $document->des, $document->h_date); // حقوق ودستمزد پرداختنی
            }
        }
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند شناسایی سنوات');
    }

    /**
     * @return bool
     */
    public function saveDocumentAdvanceMoney()
    {
        if ($this->getSalaryPeriodItems()->sum('advance_money') > 0 || $this->getSalaryPeriodItems()->andWhere('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.salary_decrease")>0')->one() !== null) {
            $document = new Document();
            $document->type = Document::TYPE_SALARY_PERIOD_ADVANCE_MONEY;
            $document->is_auto = 1;
            $document->model_id = $this->id;
            $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
            $document->des = "کسر مساعده حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
            $flag = $document->save();

            foreach ($this->getSalaryPeriodItems()->andWhere('advance_money>0')->all() as $item) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id, $item->advance_money, 0, $document->des, $document->h_date);
                /****************** بستانکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_debtor_advance_money', true), $item->employee->account_id, 0, $item->advance_money, $document->des, $document->h_date);
            }
            foreach ($this->getSalaryPeriodItems()->andWhere('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.salary_decrease")>0')->all() as $item) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id, $item->salary_decrease, 0, $document->des . ' - کسر حقوق ', $document->h_date);
                /****************** بستانکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_debtor_salary_decrease', true), null, 0, $item->salary_decrease, $document->des . ' - کسر حقوق ' . ' - ' . $item->user->customer->fullName, $document->h_date);
            }
            return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند کسر مساعده حقوق');
        } else {
            return true;
        }
    }


    /**
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     * مزایای غیر نقدی
     */
    public function saveDocumentNonCashPayment()
    {
        if ($this->getSalaryPeriodItems()->sum('non_cash_commission') > 0) {
            $document = new Document();
            $document->type = Document::TYPE_SALARY_PERIOD_NON_CASH_PAYMENT;
            $document->is_auto = 1;
            $document->model_id = $this->id;
            $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
            $document->des = "مزایای غیر نقدی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
            $flag = $document->save();

            foreach ($this->getSalaryPeriodItems()->andWhere('non_cash_commission>0')->all() as $item) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id, $item->non_cash_commission, 0, $document->des, $document->h_date);
                /****************** بستانکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_non_cash_payment_m_id', true), $item->employee->account_id, 0, $item->non_cash_commission, $document->des, $document->h_date);

            }
            return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند مزایای غیر نقدی حقوق');
        } else {
            return true;
        }
    }

    /**
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     * بیمه تکمیلی
     */
    public function saveDocumentInsuranceAddition()
    {
        if ($this->getSalaryPeriodItems()->sum('insurance_addition') > 0) {
            $document = new Document();
            $document->type = Document::TYPE_SALARY_INSURANCE_ADDITION;
            $document->is_auto = 1;
            $document->model_id = $this->id;
            $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
            $document->des = "بیمه تکمیلی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
            $flag = $document->save();

            foreach ($this->getSalaryPeriodItems()->andWhere('insurance_addition>0')->all() as $item) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id, $item->insurance_addition, 0, $document->des, $document->h_date);
                /****************** بستانکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_insurance_addition_m_id', true), $item->employee->account_id, 0, $item->insurance_addition, $document->des, $document->h_date);

            }
            return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند بیمه تکمیلی حقوق');
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function saveDocumentAdvanceMoneyReward()
    {
        if ($this->getSalaryPeriodItems()->sum('advance_money') > 0 || $this->getSalaryPeriodItems()->andWhere('JSON_EXTRACT(' . SalaryPeriodItems::tableName() . '.`additional_data`, "$.salary_decrease")>0')->one() !== null) {
            $document = new Document();
            $document->type = Document::TYPE_SALARY_PERIOD_ADVANCE_MONEY;
            $document->is_auto = 1;
            $document->model_id = $this->id;
            $document->h_date = Yii::$app->jdf->jdate("Y/m/d", $this->end_date);
            $document->des = "کسر مساعده حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
            $flag = $document->save();

            foreach ($this->getSalaryPeriodItems()->andWhere('advance_money>0')->all() as $item) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $item->employee->account_id, $item->advance_money, 0, $document->des, $document->h_date);
                /****************** بستانکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_debtor_advance_money', true), $item->employee->account_id, 0, $item->advance_money, $document->des, $document->h_date);
            }
            return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند کسر مساعده حقوق');
        } else {
            return true;
        }
    }


    /**
     * @return bool
     */
    public function saveDocumentPayment()
    {
        $document = new Document();
        $document->type = Document::TYPE_SALARY_PERIOD_PAYMENT;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = $this->payment_date;
        $document->des = "پرداختی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
        $flag = $document->save();
        $totalAmount = 0;
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            $amount = BalanceDetailed::getBalance(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id) * (-1);
            if ($amount > 0 && $item->can_payment == Helper::CHECKED) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('salary_period_payment_m_id', true), $item->employee->account_id, $amount, 0, $document->des, $document->h_date);
                $totalAmount += $amount;
            }
        }
        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_interface_salary_period_item', true), null, 0, $totalAmount, $document->des, $document->h_date);
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند پرداخت حقوق');

    }

    /**
     * @return bool
     */
    public function saveDocumentPaymentReward()
    {
        $document = new Document();
        $document->type = Document::TYPE_SALARY_PERIOD_PAYMENT;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = $this->payment_date;
        $document->des = "پرداختی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
        $flag = $document->save();
        $totalAmount = 0;
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            $amount = BalanceDetailed::getBalance(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $item->employee->account_id) * (-1);
            if ($amount > 0 && $item->can_payment == Helper::CHECKED) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $item->employee->account_id, $amount, 0, $document->des, $document->h_date);
                $totalAmount += $amount;
            }
        }
        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_interface_salary_period_item', true), null, 0, $totalAmount, $document->des, $document->h_date);
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند پرداخت حقوق');

    }

    /**
     * @return bool
     */
    public function saveDocumentPaymentYear()
    {
        $document = new Document();
        $document->type = Document::TYPE_SALARY_PERIOD_PAYMENT;
        $document->is_auto = 1;
        $document->model_id = $this->id;
        $document->h_date = $this->payment_date;
        $document->des = "پرداختی حقوق " . $this->title . ' - ' . Yii::$app->jdf->jdate("Y/m/d", $this->start_date);
        $flag = $document->save();
        $totalAmount = 0;
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            $amount = BalanceDetailed::getBalance(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $item->employee->account_id) * (-1);
            if ($amount > 0) {
                /** @var SalaryPeriodItems $item * */
                /****************** بدهکار ******************/
                $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('reward_period_payment_m_id', true), $item->employee->account_id, $amount, 0, $document->des, $document->h_date);
                $totalAmount += $amount;
            }
        }
        /****************** بستانکار ******************/
        $flag = $flag && $document->saveDetailWitDefinite(Module::getInstance()->settings::get('m_interface_salary_period_item', true), null, 0, $totalAmount, $document->des, $document->h_date);
        return $flag && $document->validateTaraz() && $this->pushDocument($document->id, 'سند پرداخت حقوق');

    }


    /**
     * @param $type
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteDocument($type)
    {
        $document = Document::find()->findByModel($this->id)->findByType($type)->one();
        if ($document !== null) {
            if (!$document->canDelete(false, false)) {
                $this->error_msq = 'امکان حذف سند حسابداری وجود ندارد.';
                return false;
            }
            if (!$document->delete()) {
                $this->error_msq = 'امکان حذف سند حسابداری وجود ندارد.';
                return false;
            }
            return $this->popDocumentWithKey($document->id);
        }
        return true;

    }

    /**
     * @return bool
     *  ثبت کسر سکه
     */
    public function saveDecreasePoint()
    {
        $flag = true;
        foreach ($this->getSalaryPeriodItems()->all() as $item) {
            /** @var SalaryPeriodItems $item * */
            if ($item->count_point > 0) {
                $model = new UserPoints([
                    'model_id' => $this->id,
                    'model_class' => self::class,
                    'user_id' => $item->user_id,
                    'type' => UserPoints::TYPE_DECREASE,
                    'creditor' => $item->count_point,
                    'debtor' => 0,
                    'reason' => UserPoints::REASON_PAYMENT,
                    'status' => UserPoints::STATUS_ACTIVE,
                    'description' => "واریز حقوق " . $this->title,
                ]);
                $flag = $flag && $model->save();
            }
        }
        return $flag;
    }

    /**
     * @return bool
     */
    public function deleteDecreasePoint()
    {
        $findModel = UserPoints::find()->byModelClass(self::class)->byModelId($this->id)->byReason(UserPoints::REASON_PAYMENT)->limit(1)->one();
        if ($findModel !== null) {
            return UserPoints::deleteAll(['model_class' => self::class, 'model_id' => $this->id, 'reason' => UserPoints::REASON_PAYMENT]) > 0;
        }
        return true;
    }

    /**
     * @return bool
     * کارمندانی که ترک کار شده اند و در دوره ثبت نشده اند.
     */
    public function addEndWorkEmployee()
    {
        $flag = true;
        if (($previousModel = self::find()->byPrevious($this->workshop_id, $this->start_date)->limit(1)->one()) !== null) {
            foreach ($previousModel->getSalaryPeriodItems()->joinWith(['employee'])
                         ->andWhere(['>=', 'JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")', Yii::$app->jdate->date("Y/m/d", $previousModel->start_date)])
                         ->andWhere(['=', 'JSON_EXTRACT(' . EmployeeBranchUser::tableName() . '.`additional_data`, "$.end_work")', Yii::$app->jdate->date("Y/m/d", $previousModel->end_date)])
                         ->andWhere(['NOT IN', SalaryPeriodItems::tableName() . '.user_id', $this->getSalaryPeriodItems()->select(['user_id'])])
                         ->all() as $employeeLost) {
                /** @var SalaryPeriodItems $employeeLost */
                $model = new SalaryPeriodItems([
                    'period_id' => $this->id,
                    'user_id' => $employeeLost->user_id,
                ]);
                $model->description = 'ترک کار ' . $employeeLost->employee->end_work;
                $flag = $flag && $model->save(false);
            }

        }
        return $flag;
    }

    /**7
     * @return mixed
     */
    public function getDocumentLink()
    {
        $link = [];
        $link['DocumentSearch'] = [];
        $link['DocumentSearch']['type'] = [];
        $link['DocumentSearch']['model_id'] = $this->id;
        $link['DocumentSearch']['type'][] = Document::TYPE_SALARY_PERIOD;
        $link['DocumentSearch']['type'][] = Document::TYPE_SALARY_PERIOD_ADVANCE_MONEY;
        $link['DocumentSearch']['type'][] = Document::TYPE_SALARY_PERIOD_NON_CASH_PAYMENT;
        $link['DocumentSearch']['type'][] = Document::TYPE_SALARY_INSURANCE_ADDITION;
        $link['DocumentSearch']['type'][] = Document::TYPE_SALARY_PERIOD_PAYMENT;
        return Yii::$app->urlManager->createUrl(ArrayHelper::merge(['/document/index'], $link));
    }

    public function getCreateDocumentNonCashLink()
    {
        return ['/document/create-modal', 'salary_non_cash' => $this->id];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => MutexBehavior::class,
            ],
            [
                'class' => DocumentsDataBehavior::class,
                'documentArrayField' => 'documentsArray',
                'documentClass' => Document::class,
                'documentViewUrl'=> '/document/view'
            ],
        ]);
    }
}
