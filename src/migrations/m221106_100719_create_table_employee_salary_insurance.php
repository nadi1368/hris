<?php

use yii\db\Migration;

class m221106_100719_create_table_employee_salary_insurance extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_salary_insurance}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'code' => $this->string(32)->notNull(),
                'group' => $this->string(64)->notNull(),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_salary_insurance}}', ['id', 'slave_id']);

		$this->alterColumn("{{%employee_salary_insurance}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('code', '{{%employee_salary_insurance}}', ['code', 'slave_id'], true);
        $this->createIndex('slave_id_index', '{{%employee_salary_insurance}}', ['slave_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_salary_insurance}}');
    }
}
