<?php

namespace hesabro\hris\models;


use yii\helpers\Url;

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
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getAccountSalaryTitle(bool $link = false): string
    {
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getDefiniteInsuranceTitle(bool $link = false): string
    {
        return '';
    }

    /**
     * @param bool $link
     * @return string
     */
    public function getAccountInsuranceTitle(bool $link = false): string
    {
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
