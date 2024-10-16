<?php

namespace hesabro\hris\models;

use himiklab\sortablegrid\SortableGridBehavior;

class InternalNumber extends InternalNumberBase
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => SortableGridBehavior::class,
                'sortableAttribute' => 'sort',
            ],
        ]);
    }
}