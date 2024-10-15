<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100634_create_table_advance_money extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%advance_money}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'comment' => $this->text()->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'amount' => $this->decimal(15, 0)->notNull(),
                'additional_data' => $this->json(),
                'receipt_number' => $this->string(32),
                'receipt_date' => $this->string(10),
                'status' => $this->boolean()->defaultValue('1'),
                'doc_id' => $this->integer(),
                'reject_comment' => $this->text(),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%advance_money}}', ['id', 'slave_id']);

        $this->alterColumn("{{%advance_money}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('manager', '{{%advance_money}}', ['user_id', 'slave_id']);
        $this->createIndex('slave_id_index', '{{%advance_money}}', ['slave_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%advance_money}}');
    }
}
