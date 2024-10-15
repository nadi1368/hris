<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Class m221116_043716_create_table_mbt_employee_salary_items_addition
 */
class m221116_043716_create_table_employee_salary_items_addition extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_salary_items_addition}}',
            [
                'id' => $this->integer()->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'kind' => $this->integer()->defaultValue('0'),
                'type' => $this->integer()->defaultValue('0'),
                'second' => $this->integer()->notNull(),
                'from_date' => $this->integer()->notNull(),
                'to_date' => $this->integer()->notNull()->defaultValue('0'),
                'description' => $this->text()->notNull(),
                'status' => $this->integer()->defaultValue('1'),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned(),
                'created' => $this->integer()->unsigned(),
                'changed' => $this->integer(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_salary_items_addition}}', ['id', 'slave_id']);
        $this->alterColumn("{{%employee_salary_items_addition}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('user_id', '{{%employee_salary_items_addition}}', ['user_id']);
        $this->createIndex('slave_id_index', '{{%employee_salary_items_addition}}', ['slave_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_salary_items_addition}}');
    }
}
