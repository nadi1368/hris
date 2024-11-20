<?php

namespace hesabro\hris\models;

use hesabro\automation\models\AuLetter;
use hesabro\helpers\traits\ModelHelper;
use hesabro\hris\Module;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\bootstrap4\ActiveForm;

class LetterBase extends Model
{
    use ModelHelper;

    // Configs

    const SCENARIO_CONFIRM = 'confirm';

    const SCENARIO_REJECT = 'reject';

    public array $variables = [];

    public EmployeeRequest|int|null $employeeRequest = null;

    public ContractTemplates|null $contractTemplate = null;

    public string|null $date = null;

    public mixed $rejectDescription = null;

    public function rules()
    {
        return [
            ['date', 'required', 'on' => [self::SCENARIO_CONFIRM]],
            ['rejectDescription', 'required', 'on' => [self::SCENARIO_REJECT]],
            [['variables'], 'safe', 'on' => [self::SCENARIO_CONFIRM]]
        ];
    }

    public function attributeLabels()
    {
        return [
            'variables' => Module::t('module', 'Variables'),
            'date' => Module::t('module', 'Date') . ' ' . Module::t('module', 'Letter'),
            'rejectDescription' => Module::t('module', 'Reject Description'),
        ];
    }

    // Init

    public function init()
    {
        parent::init();

        if ($this->employeeRequest && !($this->employeeRequest instanceof EmployeeRequest)) {
            $this->employeeRequest = EmployeeRequest::findOne($this->employeeRequest);
        }

        $this->contractTemplate = $this->employeeRequest->contractTemplate;
        $this->initVariables();
    }

    private function initVariables()
    {
        if ($this->employeeRequest) {
            $userStaticVariables = EmployeeBranchUser::itemAlias('insuranceDataDefaultVariables');
            $employeeBranchUser = EmployeeBranchUser::findOne($this->employeeRequest->user_id);
            $insuranceData = $employeeBranchUser->getInsuranceData(true);
            foreach (($this->contractTemplate?->variables ?: []) as $key => $value) {
                $this->variables[$key] = isset($this->variables[$key]) ? $this->variables[$key] : (array_key_exists($key, $userStaticVariables) ? ($insuranceData[$key] ?? null) : null);
            }
        }
    }

    // Generators

    public function getVariablesInput(ActiveForm $form)
    {
        $inputs = [];

        foreach ($this->variables as $variable => $value) {
            $inputs['{' . $variable . '}'] = $form->field($this, "variables[$variable]", [
                'options' => [
                    'class' => 'd-inline-block mt-4',
                ]
            ])->textInput([
                'value' => $value,
                'class' => 'letter-input'
            ])->label(false);
        }

        return $inputs;
    }

    public function getVariablesValue()
    {
        $values = [];

        foreach ($this->variables as $variable => $value) {
            $values['{' . $variable . '}'] = $value;
        }

        return $values;
    }

    // Actions

    public function confirm(): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($this->contractTemplate->au_letter_type) {
                $auLetter = $this->createAuLetter();
                if (!$auLetter) {
                    $transaction->rollBack();
                    return false;
                }
                $this->employeeRequest->au_letter_id = $auLetter->id;
            }

            $confirm = $this->employeeRequest->confirm();
            $transaction->commit();

            return $confirm;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function reject(): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->employeeRequest->reject_description = $this->rejectDescription;
            $reject = $this->employeeRequest->reject();
            $transaction->commit();

            return $reject;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function createAuLetter(): AuLetter
    {
        $auLetter = new AuLetter();
        $auLetter->type = $this->contractTemplate->au_letter_type;
        $auLetter->input_type = 0;
        $auLetter->title = implode(' ', [
            Module::t('module', 'Letter'),
            "({$this->contractTemplate->title})",
            Module::t('module', 'For'),
            $this->employeeRequest->user->fullName
        ]);
        $auLetter->body = (Yii::$app->getView())->renderFile('@hesabro/hris/views/employee-request/letter/template.php', [
            'letter' => $this
        ]);
        $auLetter->save(false);

        return $auLetter;
    }
}

