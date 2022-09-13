<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m220912_164620_add_allowance_column_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'allowance', $this->integer()->notNull());
        $this->addColumn('{{%user}}', 'allowance_updated_at', $this->integer()->notNull());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'allowance');
        $this->dropColumn('{{%user}}', 'allowance_updated_at');
    }
}
