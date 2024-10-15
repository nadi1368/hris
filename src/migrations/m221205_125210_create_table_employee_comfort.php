<?php

use yii\db\Migration;

/**
 * Class m221205_125210_create_table_employee_comfort
 */
class m221205_125210_create_table_employee_comfort extends Migration
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
            '{{%employee_comfort}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->string(128)->notNull(),
                'type' => $this->integer()->notNull(),
                'expire_time' => $this->integer()->notNull()->defaultValue(0),
                'status' => $this->integer(1)->notNull()->defaultValue(1),
                'type_limit' => $this->integer()->notNull(),
                'count_limit' => $this->integer()->notNull()->defaultValue(0),
                'amount_limit' => $this->decimal(15,0)->notNull()->defaultValue(0),
                'description' => $this->text(),
                'additional_data' => $this->json(),
                'created' => $this->integer()->notNull(),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );


        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_comfort}}', ['id', 'slave_id']);
        $this->alterColumn("{{%employee_comfort}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id_index', '{{%employee_comfort}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_comfort}}');
    }
}
