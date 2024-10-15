<?php



use yii\db\Migration;

/**
 * Class m240605_061746_alter_employee_contract_templates_table
 */
class m240605_061746_alter_employee_contract_templates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%employee_contract_templates}}', 'type', $this->tinyInteger()->defaultValue(1)->after('title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_contract_templates}}', 'type');
    }
}
