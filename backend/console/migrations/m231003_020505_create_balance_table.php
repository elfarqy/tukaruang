<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%balance}}`.
 */
class m231003_020505_create_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%balance}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger(),
            'amount' => $this->double(),
            'currency' => $this->string(5),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
        $this->addForeignKey('user-balance', 'balance', 'user_id', 'user', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user-balance', 'balance');
        $this->dropTable('{{%balance}}');
    }
}
