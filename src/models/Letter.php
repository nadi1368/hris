<?php

namespace hesabro\hris\models;

use hesabro\hris\Module;
use Yii;

class Letter extends LetterBase
{
    public function undo(): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($this->employeeRequest->reject_description) {
                $this->employeeRequest->reject_description = null;
            }

            if ($this->employeeRequest->indicator_id) {
                // Todo: Mota: Delete AuLetter After delete letter
            }
            $undo = $this->employeeRequest->pending();
            $transaction->commit();
            return $undo;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function createIndicator(): ?Indicator
    {
        $indicator = new Indicator([
            'scenario' => Indicator::SCENARIO_CREATE_LETTER,
            'type' => Indicator::TYPE_EXPORT,
            'status' => Indicator::STATUS_ACTIVE,
            'date' => $this->date,
            'title' => implode(' ', [
                Module::t('module', 'Letter'),
                "({$this->contractTemplate->title})",
                Module::t('module', 'For'),
                $this->employeeRequest->user->fullName
            ]),
            'file_text' => ''
        ]);

        $indicator->loadAttributes();
        $saveIndicator = $indicator->save();

        if ($saveIndicator && !$indicator->document_number) {
            $indicator->document_number = $indicator->id;
            $saveIndicator = $indicator->save();
        }

        return $saveIndicator ? $indicator : null;
    }
}