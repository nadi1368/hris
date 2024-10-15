<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m230101_081554_create_table_request_leave extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%request_leave}}',
            [
                'id' => $this->integer()->notNull(),
                'branch_id' => $this->integer()->unsigned()->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'manager_id' => $this->integer()->unsigned()->notNull(),
                'type' => $this->integer()->defaultValue('1'),
                'description' => $this->text()->notNull(),
                'from_date' => $this->integer()->notNull(),
                'to_date' => $this->integer()->notNull(),
                'status' => $this->integer()->defaultValue('1'),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned(),
                'created' => $this->integer()->unsigned(),
                'changed' => $this->integer(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%request_leave}}', ['id', 'slave_id']);
        $this->alterColumn("{{%request_leave}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id_index', '{{%request_leave}}', ['slave_id']);
        $this->createIndex('branch_id', '{{%request_leave}}', ['branch_id']);
        $this->createIndex('manager_id', '{{%request_leave}}', ['manager_id']);
        $this->createIndex('user_id', '{{%request_leave}}', ['user_id']);

    }

    public function safeDown()
    {
        $this->dropTable('{{%request_leave}}');
    }
}
