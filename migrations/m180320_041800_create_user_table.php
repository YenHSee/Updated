<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180320_041800_create_user_table extends Migration
{
   /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'password' => $this->string(),
            'role' => $this->string(),
            'name' => $this->string(),
            'email' => $this->string()->notNull(),
            'status' => $this->string(),
            'remark' => $this->string(),
            'security_question' => $this->string(),
            'security_answer' => $this->string(),
            'last_logging' => 'timestamp on update current_timestamp',
            'created_at' => $this->timestamp()->defaultValue(0), 
            'updated_at' => 'timestamp on update current_timestamp', 
            'is_deleted' => $this->boolean()->defaultValue(false), 
        ]);

        $this->insert('user', [
            'username' => 'Yhs',
            'password' => 'DoIp8YQdnX0XE',
            'role' => 'Admin',
            'name' => 'YenHong See',
            'email' => 'seeyhong@hotmail.com',
            'status' => 'Activated',
            'remark' => 'Its An Admin',
            'security_question' => '1',
            'security_answer' => 'Yes',
            'last_logging' => NULL,
            'created_at' => NULL,
            'updated_at' => NULL,
            'is_deleted' => 0,
        ]);
        //$this->alterColumn('{{$user}}', 'id', $this->integer()->notNull(). 'AUTO_INCREMENT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('user', ['id' => 1]);
        $this->dropTable('user');
    }
}
