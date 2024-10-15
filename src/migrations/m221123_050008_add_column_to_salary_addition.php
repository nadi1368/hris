<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Class m221123_050008_add_column_to_salary_addition
 */
class m221123_050008_add_column_to_salary_addition extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee_salary_items_addition}}', 'is_auto', $this->integer()->defaultValue(0)->null());
        $this->addColumn('{{%employee_salary_items_addition}}', 'period_id', $this->integer()->defaultValue(0)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_salary_items_addition}}', 'is_auto');
        $this->dropColumn('{{%employee_salary_items_addition}}', 'period_id');
    }
}
