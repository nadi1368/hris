<?php

namespace hesabro\hris\models;


use yii\helpers\Url;
use common\models\Account;
use common\models\AccountDefinite;

/**
 * Class
 * @package hesabro\hris\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class  EmployeeBranch extends EmployeeBranchBase
{

    /**
     * @return string
     */
    public function getDefiniteUrl(): string
    {
        return Url::to(['/account-definite/find', 'level' => 3]);
    }

    /**
     * @return string
     */
    public function getAccountUrl(): string
    {
        return Url::to(['/account/get-account']);
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getDefiniteSalaryTitle(bool $link = false): string
    {
        if ($this->definite_id_salary && ($model = AccountDefinite::findOne($this->definite_id_salary)) !== null) {
            return $link ? $model->getFullName(true) : $model->halfName;
        }
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getAccountSalaryTitle(bool $link = false): string
    {
        if ($this->account_id_salary && ($model = Account::findOne($this->account_id_salary)) !== null) {
            return $model->getFullName(true);
        }
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getDefiniteInsuranceTitle(bool $link = false): string
    {
        if ($this->definite_id_insurance_owner && ($model = AccountDefinite::findOne($this->definite_id_insurance_owner)) !== null) {
            return $link ? $model->getFullName(true) : $model->halfName;
        }
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getAccountInsuranceTitle(bool $link = false): string
    {
        if ($this->account_id_insurance_owner && ($model = Account::findOne($this->account_id_insurance_owner)) !== null) {
            return $model->getFullName(true);
        }
        return '';
    }

    /**
     * @return bool
     */
    public function canSaveDocument(): bool
    {
        return $this->definite_id_salary > 0 && $this->definite_id_insurance_owner;
    }
}
