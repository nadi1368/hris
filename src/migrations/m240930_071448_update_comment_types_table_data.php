<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Class m240930_071448_update_comment_types_table_data
 */
class m240930_071448_update_comment_types_table_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%comments_type}}', [
            'key' => 'REQUEST_ADVANCE_MONEY',
        ], [
            'key' => 'REQUEST_DRAFT_REPORT'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update('{{%comments_type}}', [
            'key' => 'REQUEST_DRAFT_REPORT',
        ], [
            'key' => 'REQUEST_ADVANCE_MONEY'
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240930_071448_update_comment_types_table_data cannot be reverted.\n";

        return false;
    }
    */
}
