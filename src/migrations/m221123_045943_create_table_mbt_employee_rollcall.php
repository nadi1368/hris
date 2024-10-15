<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Class m221123_045943_create_table_mbt_employee_rollcall
 */
class m221123_045943_create_table_mbt_employee_rollcall extends Migration
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
            '{{%employee_rollcall}}',
            [
                'id' => $this->integer()->notNull(),
                'user_id' => $this->integer()->notNull(),
                'date' => $this->string(10)->notNull(),
                'status' => $this->string(128)->notNull(),
                'total' => $this->time()->notNull(),
                'shift' => $this->time()->notNull(),
                'over_time' => $this->integer()->notNull(),
                'low_time' => $this->integer()->notNull(),
                'mission_time' => $this->integer()->notNull(),
                'leave_time' => $this->integer()->notNull(),
                'in_1' => $this->time()->notNull(),
                'out_1' => $this->time()->notNull(),
                'in_2' => $this->time()->notNull(),
                'out_2' => $this->time()->notNull(),
                'in_3' => $this->time()->notNull(),
                'out_3' => $this->time()->notNull(),
                't_id' => $this->integer()->notNull(),
                'period_id' => $this->integer()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_rollcall}}', ['id', 'slave_id']);
        $this->alterColumn("{{%employee_rollcall}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('user_id', '{{%employee_rollcall}}', ['user_id']);
        $this->createIndex('t_id', '{{%employee_rollcall}}', ['t_id']);
        $this->createIndex('slave_id_index', '{{%employee_rollcall}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_rollcall}}');
    }
}
