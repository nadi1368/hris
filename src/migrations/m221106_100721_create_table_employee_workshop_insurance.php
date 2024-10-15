<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100721_create_table_employee_workshop_insurance extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_workshop_insurance}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'code' => $this->string(32)->notNull(),
                'title' => $this->string(64)->notNull(),
                'manager' => $this->string(32)->notNull(),
                'additional_data' => $this->text(),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_workshop_insurance}}', ['id', 'slave_id']);

        $this->alterColumn("{{%employee_workshop_insurance}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('code', '{{%employee_workshop_insurance}}', ['code', 'slave_id'], true);
        $this->createIndex('slave_id_index', '{{%employee_workshop_insurance}}', ['slave_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_workshop_insurance}}');
    }
}
