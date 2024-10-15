<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;

/**
 * Class m240912_061218_alter_table_workshop
 */
class m240912_061218_alter_table_workshop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `employee_workshop_insurance` CHANGE `additional_data` `additional_data` JSON NULL DEFAULT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
