<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100932_create_table_employee_user_contracts extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_user_contracts}}',
            [
                'id' => $this->integer()->notNull(),
                'contract_id' => $this->integer(),
                'branch_id' => $this->integer(),
                'user_id' => $this->integer()->unsigned(),
                'start_date' => $this->string(),
                'end_date' => $this->string(),
                'month' => $this->float(),
                'variables' => $this->json()->notNull(),
                'additional_data' => $this->json()->notNull(),
                'contract_clauses' => $this->json()->notNull(),
                'status' => $this->integer()->notNull()->defaultValue('1'),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'shelf_id' => $this->integer(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_user_contracts}}', ['id', 'slave_id']);

        $this->alterColumn("{{%employee_user_contracts}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('slave_id_index', '{{%employee_user_contracts}}', ['slave_id']);

        $this->addForeignKey(
            'employee_user_contracts_ibfk_1',
            '{{%employee_user_contracts}}',
            ['contract_id', 'slave_id'],
            '{{%employee_contract_templates}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
        $this->addForeignKey(
            'employee_user_contracts_ibfk_2',
            '{{%employee_user_contracts}}',
            ['user_id', 'slave_id'],
            '{{%user}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
        $this->addForeignKey(
            'mbt_employee_user_contracts_ibfk_shelf_id',
            '{{%employee_user_contracts}}',
            ['shelf_id', 'slave_id'],
            '{{%employee_user_contracts_shelves}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_user_contracts}}');
    }
}
