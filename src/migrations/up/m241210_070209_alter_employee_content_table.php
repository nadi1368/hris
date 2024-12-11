<?php

use yii\db\Migration;

/**
 * Class m241210_070209_alter_employee_content_table
 */
class m241210_070209_alter_employee_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%employee_content}}', 'description');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%employee_content}}', 'description', $this->text());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241210_070209_alter_employee_content_table cannot be reverted.\n";

        return false;
    }
    */
}
