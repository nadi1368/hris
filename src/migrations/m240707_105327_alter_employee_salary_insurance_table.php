<?php

use yii\db\Migration;

/**
 * Class m240707_105327_alter_salary_insurance_table
 */
class m240707_105327_alter_employee_salary_insurance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee_salary_insurance}}', 'tag_id', $this->integer()->unsigned()->null()->after('group'));

        $this->createIndex('idx-employee_salary_insurance-tag_id', '{{%employee_salary_insurance}}', 'tag_id');

        $this->addForeignKey(
            'fk-employee_salary_insurance-tag_id',
            '{{%employee_salary_insurance}}',
            'tag_id',
            '{{%tags}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee_salary_insurance}}', 'tag_id');
    }
}
