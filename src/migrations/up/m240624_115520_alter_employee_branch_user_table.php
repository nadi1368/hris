<?php



use yii\db\Migration;

/**
 * Class m240624_115520_alter_employee_branch_user_table
 */
class m240624_115520_alter_employee_branch_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee_branch_user}}', 'pending_data', $this->json()->null()->after('additional_data'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_branch_user}}', 'pending_data');
    }
}
