<?php

namespace hesabro\hris\widgets;

use hesabro\helpers\Module;
use hesabro\hris\models\EmployeeBranch;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

class EmployeeBranchSelect2 extends Select2
{
    public string $relation = 'branch';

    public string $label = 'title';

    public function init()
    {
        parent::init();

        $this->setData();
        $this->setOptions();
        $this->setPluginOptions();
        $this->setInitValueText();
    }

    private function setInitValueText()
    {
        $this->initValueText = $this->model->{$this->attribute} ? array_map(function ($item) {
            return $item->{$this->label};
        }, $this->model->{$this->relation} ?: []) : [];
    }

    private function setOptions()
    {
        $this->options = array_merge($this->options, [
            'placeholder' => Module::t('module', 'Search'),
            'dir' => 'rtl'
        ]);
    }

    private function setPluginOptions()
    {
        $this->pluginOptions = array_merge($this->pluginOptions, [
            'allowClear' => true,
        ]);
    }

    private function setData()
    {
        $this->data = EmployeeBranch::itemAlias('List');
    }
}
