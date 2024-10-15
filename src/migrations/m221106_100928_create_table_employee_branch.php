<?php

use yii\db\Migration;

class m221106_100928_create_table_employee_branch extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_branch}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'title' => $this->string(32)->notNull(),
                'manager' => $this->integer()->unsigned()->notNull(),
                'status' => $this->boolean()->defaultValue('1'),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_branch}}', ['id', 'slave_id']);

		$this->alterColumn("{{%employee_branch}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('manager', '{{%employee_branch}}', ['manager', 'slave_id']);
        $this->createIndex('slave_id_index', '{{%employee_branch}}', ['slave_id']);

        $this->addForeignKey(
            'employee_branch_ibfk_1',
            '{{%employee_branch}}',
            ['manager', 'slave_id'],
            '{{%user}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_branch}}');
    }
}
