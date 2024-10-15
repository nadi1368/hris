<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Class m240520_133327_create_table_rate_of_year
 */
class m240520_133327_create_table_rate_of_year extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_rate_of_year}}',
            [
                'id' => $this->integer()->notNull(),
                'year' => $this->integer()->notNull(),
                'rate_of_day' => $this->decimal(15, 0)->notNull(),
                'status' => $this->tinyInteger()->notNull()->defaultValue('1'),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'created_by' => $this->integer()->unsigned(),
                'updated_at' => $this->integer()->unsigned()->notNull(),
                'updated_by' => $this->integer()->unsigned(),
                'deleted_at' => $this->integer()->unsigned()->notNull()->defaultValue('0'),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );


        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_rate_of_year}}', ['id', 'slave_id']);
        $this->alterColumn("{{%employee_rate_of_year}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%employee_rate_of_year}}', ['slave_id']);
        $this->createIndex('year_unique', '{{%employee_rate_of_year}}', ['slave_id', 'year', 'status', 'deleted_at'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_rate_of_year}}');
    }
}
