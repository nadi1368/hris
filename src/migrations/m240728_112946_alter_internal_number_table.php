<?php

use yii\db\Migration;

/**
 * Class m240728_112946_alter_internal_number_table
 */
class m240728_112946_alter_internal_number_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%internal_number}}', 'additional_data', $this->json()->after('user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%internal_number}}', 'additional_data');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240728_112946_alter_internal_number_table cannot be reverted.\n";

        return false;
    }
    */
}
