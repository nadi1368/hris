<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100718_create_table_employee_salary_base extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_salary_base}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'year' => $this->string(4)->notNull(),
                'group' => $this->string(32)->notNull(),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'cost_of_year' => $this->decimal(15, 0)->notNull(),
                'cost_of_work' => $this->decimal(15, 0)->notNull(),
                'cost_of_hours' => $this->decimal(15, 0)->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_salary_base}}', ['id', 'slave_id']);

        $this->alterColumn("{{%employee_salary_base}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('slave_id_index', '{{%employee_salary_base}}', ['slave_id']);
        $this->createIndex('year', '{{%employee_salary_base}}', ['year', 'group', 'slave_id'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_salary_base}}');
    }
}
