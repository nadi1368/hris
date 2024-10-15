<?php

use yii\db\Migration;

/**
 * Class m221205_125625_create_table_employee_comfort_items
 */
class m221205_125625_create_table_employee_comfort_items extends Migration
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
            '{{%employee_comfort_items}}',
            [
                'id' => $this->integer()->notNull(),
                'comfort_id' => $this->integer(),
                'user_id' => $this->integer(),
                'amount' => $this->decimal(15,0)->notNull()->defaultValue(0),
                'attach' => $this->string(128),
                'description' => $this->text(),
                'additional_data' => $this->json(),
                'status' => $this->integer(1)->notNull()->defaultValue(1),
                'created' => $this->integer()->notNull(),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );


        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_comfort_items}}', ['id', 'slave_id']);
        $this->alterColumn("{{%employee_comfort_items}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('user_id', '{{%employee_comfort_items}}', ['user_id']);
        $this->createIndex('comfort_id', '{{%employee_comfort_items}}', ['comfort_id']);
        $this->createIndex('slave_id_index', '{{%employee_comfort_items}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_comfort_items}}');
    }
}
