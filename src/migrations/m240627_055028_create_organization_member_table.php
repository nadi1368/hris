<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%organization_member}}`.
 */
class m240627_055028_create_organization_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%organization_member}}', [
            'id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'parent_id' => $this->integer()->null(),
            'updated_at' => $this->integer(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer()->unsigned()->null(),
            'updated_by' => $this->integer()->unsigned()->null(),
            'additional_data' => $this->json()->null(),
            'slave_id' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addPrimaryKey('PRIMARYKEY', '{{%organization_member}}', ['id', 'slave_id']);
        $this->alterColumn("{{%organization_member}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%organization_member}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%organization_member}}');
    }
}
