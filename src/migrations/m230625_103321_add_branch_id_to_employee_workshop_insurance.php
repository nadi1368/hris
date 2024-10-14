<?php

use yii\db\Migration;

/**
 * Class m230625_103321_add_branch_id_to_employee_workshop_insurance
 */
class m230625_103321_add_branch_id_to_employee_workshop_insurance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee_workshop_insurance}}', 'branch_id', $this->integer()->notNull()->defaultValue(1)->after('title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_workshop_insurance}}', 'branch_id');
    }
}
