<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100720_create_table_employee_user_contracts_shelves extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_user_contracts_shelves}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->string()->notNull(),
                'capacity' => $this->integer()->notNull(),
                'status' => $this->integer()->notNull()->defaultValue('1'),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_user_contracts_shelves}}', ['id', 'slave_id']);

        $this->alterColumn("{{%employee_user_contracts_shelves}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('slave_id_index', '{{%employee_user_contracts_shelves}}', ['slave_id']);
        $this->createIndex('title', '{{%employee_user_contracts_shelves}}', ['title', 'slave_id'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_user_contracts_shelves}}');
    }
}
