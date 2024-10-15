<?php

use yii\db\Migration;

/**
 * Class m240814_112056_alter_internal_numbers_table
 */
class m240814_112056_alter_internal_numbers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%internal_number}}', 'sort', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%internal_number}}', 'sort');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240814_112056_alter_internal_numbers_table cannot be reverted.\n";

        return false;
    }
    */
}
