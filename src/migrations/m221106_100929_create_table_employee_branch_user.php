<?php

use yii\db\Migration;

class m221106_100929_create_table_employee_branch_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_branch_user}}',
            [
                'user_id' => $this->integer()->unsigned()->notNull(),
                'branch_id' => $this->integer()->unsigned()->notNull(),
                'deleted_at' => $this->integer()->notNull(),
                'status' => $this->integer()->notNull()->defaultValue('1'),
                'salary' => $this->decimal(15, 0)->notNull()->defaultValue('25000000')->comment('حقوق'),
                'shaba' => $this->string(24),
                'additional_data' => $this->json(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_branch_user}}', ['user_id', 'branch_id', 'deleted_at', 'slave_id']);

        $this->createIndex('deleted_at', '{{%employee_branch_user}}', ['deleted_at', 'slave_id']);
        $this->createIndex('slave_id_index', '{{%employee_branch_user}}', ['slave_id']);

        $this->addForeignKey(
            'employee_branch_user_ibfk_1',
            '{{%employee_branch_user}}',
            ['branch_id', 'slave_id'],
            '{{%employee_branch}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
        $this->addForeignKey(
            'employee_branch_user_ibfk_2',
            '{{%employee_branch_user}}',
            ['user_id', 'slave_id'],
            '{{%user}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_branch_user}}');
    }
}
