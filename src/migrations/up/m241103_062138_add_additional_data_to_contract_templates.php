<?php

use yii\db\Migration;

/**
 * Class m241103_062138_add_addtional_data_to_contract_templates
 */
class m241103_062138_add_additional_data_to_contract_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee_contract_templates}}', 'additional_data', $this->json()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_contract_templates}}', 'additional_data');
    }
}
