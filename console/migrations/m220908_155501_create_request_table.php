<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m220908_155501_create_request_table extends Migration
{
    public function up()
    {
        $this->createTable('request', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'status' => "ENUM('Active', 'Resolved') NOT NULL DEFAULT 'Active'",
            'message' => $this->text()->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->dateTime()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'updated_at' => $this->dateTime()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    public function down()
    {
        $this->dropTable('request');
    }
}
