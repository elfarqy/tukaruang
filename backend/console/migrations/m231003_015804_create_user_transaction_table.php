<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_transaction}}`.
 */
class m231003_015804_create_user_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_transaction}}', [
            'id' => $this->bigPrimaryKey(),
            'identifier' => $this->string(26)->unique(),
            'amount' => $this->double(),
            'current_price' => $this->double(),
            'currency_source' => $this->string(),
            'currency_target' => $this->string(),
            'type' => $this->string(), // sell or buy
            'status' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_transaction}}');
    }
}
