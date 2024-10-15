<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

class m221106_100722_create_table_content extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%content}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->text(),
                'description' => $this->text(),
                'type' => $this->integer(),
                'status' => $this->integer()->defaultValue('1'),
                'created' => $this->integer(),
                'creator_id' => $this->integer(),
                'update_id' => $this->integer(),
                'changed' => $this->integer(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%content}}', ['id', 'slave_id']);

        $this->alterColumn("{{%content}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('slave_id_index', '{{%content}}', ['slave_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%content}}');
    }
}
