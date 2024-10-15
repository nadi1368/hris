<?php



use yii\db\Migration;

/**
 * Handles the creation of table `{{%employee_requests}}`.
 */
class m240605_074617_create_employee_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%employee_requests}}', [
            'id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'branch_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->tinyInteger()->notNull(),
            'additional_data' => $this->json(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_by' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'deleted_at' => $this->integer()->unsigned()->null(),
            'slave_id' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_requests}}', ['id', 'slave_id']);
        $this->alterColumn("{{%employee_requests}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%employee_requests}}', ['slave_id']);

        $this->createIndex('idx_user_id', '{{%employee_requests}}', 'user_id');
        $this->createIndex('idx_branch_id', '{{%employee_requests}}', 'branch_id');

        $this->addForeignKey(
            'fk_user_id',
            '{{%employee_requests}}',
            'user_id',
            '{{%user}}',
            'id'
        );

        $this->addForeignKey(
            'fk_branch_id',
            '{{%employee_requests}}',
            'branch_id',
            '{{%employee_branch}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_requests}}');
    }
}
