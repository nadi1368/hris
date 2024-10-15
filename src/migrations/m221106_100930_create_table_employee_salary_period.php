<?php

use yii\db\Migration;

class m221106_100930_create_table_employee_salary_period extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_salary_period}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'workshop_id' => $this->integer()->unsigned()->notNull()->defaultValue('1'),
                'title' => $this->string(32)->notNull(),
                'start_date' => $this->integer()->notNull(),
                'end_date' => $this->integer()->notNull(),
                'additional_data' => $this->json(),
                'status' => $this->integer()->notNull()->defaultValue('1'),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_salary_period}}', ['id', 'slave_id']);

		$this->alterColumn("{{%employee_salary_period}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('slave_id_index', '{{%employee_salary_period}}', ['slave_id']);
        $this->createIndex('workshop_id', '{{%employee_salary_period}}', ['workshop_id', 'slave_id']);

        $this->addForeignKey(
            'employee_salary_period_ibfk_1',
            '{{%employee_salary_period}}',
            ['workshop_id', 'slave_id'],
            '{{%employee_workshop_insurance}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_salary_period}}');
    }
}
