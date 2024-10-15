<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100717_create_table_employee_contract_templates extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_contract_templates}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->text(),
                'description' => $this->text(),
                'status' => $this->integer()->notNull()->defaultValue('1'),
                'clauses' => $this->json()->notNull(),
                'variables' => $this->json()->notNull(),
                'signatures' => $this->text(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_contract_templates}}', ['id', 'slave_id']);

        $this->alterColumn("{{%employee_contract_templates}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('slave_id_index', '{{%employee_contract_templates}}', ['slave_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_contract_templates}}');
    }
}
