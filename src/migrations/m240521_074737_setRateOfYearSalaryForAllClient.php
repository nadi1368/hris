<?php

namespace hesabro\hris\migrations;

use yii\db\Migration;
use backend\modules\master\models\Client;

/**
 * Class m240521_074737_setRateOfYearSalaryForAllClient
 */
class m240521_074737_setRateOfYearSalaryForAllClient extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $clients = Client::find()->all();
        foreach ($clients as $client) {
            /** @var Client $client */
            $sql = "INSERT INTO `employee_rate_of_year` (`id`, `year`, `rate_of_day`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `slave_id`) VALUES
                    (1, 1, 70000, 1, 1716211085, 1, 1716211220, 1, 0, {$client->id}),
                    (2, 2, 155400, 1, 1716269506, 1, 1716269506, 1, 0, {$client->id}),
                    (3, 3, 258734, 1, 1716269550, 1, 1716269550, 1, 0, {$client->id}),
                    (4, 4, 353801, 1, 1716277177, 1, 1716277177, 1, 0, {$client->id}),
                    (5, 5, 439361, 1, 1716277190, 1, 1716277190, 1, 0, {$client->id}),
                    (6, 6, 508235, 1, 1716277210, 1, 1716277210, 1, 0, {$client->id}),
                    (7, 7, 564940, 1, 1716277223, 1, 1716277223, 1, 0, {$client->id}),
                    (8, 8, 627545, 1, 1716277238, 1, 1716277238, 1, 0, {$client->id}),
                    (9, 9, 668783, 1, 1716277249, 1, 1716277249, 1, 0, {$client->id}),
                    (10, 10, 715797, 1, 1716277261, 1, 1716277261, 1, 0, {$client->id});";
            $this->execute(new \yii\db\Expression($sql));
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240521_074737_setRateOfYearSalaryForAllClient cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240521_074737_setRateOfYearSalaryForAllClient cannot be reverted.\n";

        return false;
    }
    */
}
