<?php

use yii\db\Migration;

/**
 * Class m241103_062138_add_addtional_data_to_contract_templates
 */
class m241114_075344_alter_table_employee_branch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee_branch}}', 'definite_id_salary', $this->integer()->null());
        $this->addColumn('{{%employee_branch}}', 'account_id_salary', $this->integer()->null());
        $this->addColumn('{{%employee_branch}}', 'definite_id_insurance_owner', $this->integer()->null());
        $this->addColumn('{{%employee_branch}}', 'account_id_insurance_owner', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_branch}}', 'definite_id_salary');
        $this->dropColumn('{{%employee_branch}}', 'account_id_salary');
        $this->dropColumn('{{%employee_branch}}', 'definite_id_insurance_owner');
        $this->dropColumn('{{%employee_branch}}', 'account_id_insurance_owner');
    }
}
