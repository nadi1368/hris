<?php

use yii\db\Migration;

class m221106_100931_create_table_employee_salary_period_items extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%employee_salary_period_items}}',
            [
                'id' => $this->integer()->unsigned()->notNull(),
                'period_id' => $this->integer()->unsigned()->notNull(),
                'user_id' => $this->integer()->unsigned()->notNull(),
                'hours_of_work' => $this->integer()->notNull(),
                'basic_salary' => $this->decimal(15, 0)->notNull(),
                'cost_of_house' => $this->decimal(15, 0)->notNull(),
                'cost_of_food' => $this->decimal(15, 0)->notNull(),
                'cost_of_children' => $this->decimal(15, 0)->notNull(),
                'count_of_children' => $this->integer()->notNull(),
                'cost_of_year' => $this->decimal(15, 0)->notNull()->comment('حق سنوات'),
                'rate_of_year' => $this->decimal(15, 0)->notNull()->comment('نرخ سنوات سلالانه'),
                'hours_of_overtime' => $this->float()->notNull()->defaultValue('0')->comment('تعداد ساعات اضافه کاری'),
                'holiday_of_overtime' => $this->float()->notNull()->defaultValue('0')->comment('تعداد ساعات تعطیل کاری'),
                'night_of_overtime' => $this->float()->notNull()->defaultValue('0')->comment('تعداد ساعات شب کاری'),
                'commission' => $this->decimal(15, 0)->notNull()->defaultValue('0')->comment('پورسانت'),
                'insurance' => $this->decimal(15, 0)->notNull()->defaultValue('0')->comment('حق بیمه'),
                'insurance_owner' => $this->decimal(15, 0)->notNull()->comment('بیمه کارفرا'),
                'tax' => $this->decimal(15, 0)->notNull()->defaultValue('0')->comment('مالیات'),
                'cost_of_trust' => $this->decimal(15, 0)->notNull()->defaultValue('0')->comment('حق مسئولیت'),
                'total_salary' => $this->decimal(15, 0)->notNull(),
                'advance_money' => $this->decimal(15, 0)->notNull()->comment('مبلغ مساعده این ماه'),
                'payment_salary' => $this->decimal(15, 0)->notNull(),
                'can_payment' => $this->integer()->notNull()->defaultValue('1'),
                'creator_id' => $this->integer()->unsigned()->notNull(),
                'update_id' => $this->integer()->unsigned()->notNull(),
                'created' => $this->integer()->unsigned()->notNull(),
                'changed' => $this->integer()->unsigned()->notNull(),
                'additional_data' => $this->json(),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );

        $this->addPrimaryKey('PRIMARYKEY', '{{%employee_salary_period_items}}', ['id', 'slave_id']);

		$this->alterColumn("{{%employee_salary_period_items}}", 'id', $this->integer()->unsigned()->notNull()->append('AUTO_INCREMENT'));

        $this->createIndex('period_id', '{{%employee_salary_period_items}}', ['period_id', 'slave_id']);
        $this->createIndex('period_id_2', '{{%employee_salary_period_items}}', ['period_id', 'user_id', 'slave_id'], true);
        $this->createIndex('slave_id_index', '{{%employee_salary_period_items}}', ['slave_id']);
        $this->createIndex('user_id', '{{%employee_salary_period_items}}', ['user_id', 'slave_id']);

        $this->addForeignKey(
            'employee_salary_period_items_ibfk_1',
            '{{%employee_salary_period_items}}',
            ['period_id', 'slave_id'],
            '{{%employee_salary_period}}',
            ['id', 'slave_id'],
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%employee_salary_period_items}}');
    }
}
