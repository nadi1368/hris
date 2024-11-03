<?php

namespace hesabro\hris\models;

use hesabro\automation\models\AuLetter;
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

            if ($this->employeeRequest->au_letter_id) {
                $auLetter = AuLetter::findOne($this->employeeRequest->au_letter_id);
                if ($auLetter->canDelete()) {
                    $auLetter->softDelete();
                }
            }
            $undo = $this->employeeRequest->pending();
            $transaction->commit();
            return $undo;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}