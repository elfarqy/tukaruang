<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%activity}}`.
 */
class m231003_020110_create_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%activity}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'user_transaction_id' => $this->bigInteger(),
            'message' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
        $this->addForeignKey('user-activity', 'activity', 'user_id', 'user', 'id');
        $this->addForeignKey('transaction-activity', 'activity', 'user_transaction_id', 'user_transaction', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user-activity', 'activity');
        $this->dropForeignKey('transaction-activity', 'activity');
        $this->dropTable('{{%activity}}');
    }
}
