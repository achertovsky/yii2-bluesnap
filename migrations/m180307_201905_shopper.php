<?php

use yii\db\Migration;

class m180307_201905_shopper extends Migration
{
    public $tableName = '{{%bluesnap_shopper}}';
    
    public function safeUp()
    {
        if (empty($this->db->getTableSchema($this->tableName))) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'created_at' => $this->integer(),
                    'updated_at' => $this->integer(),
                    'user_id' => $this->integer(),
                    'shopper_id' => $this->integer(),
                    'web_info' => $this->text(),
                    'fraud_info' => $this->text(),
                    'shopper_info' => $this->text(),
                    'wallet_id' => $this->integer(),
                ]
            );
            $this->addForeignKey('fk_user_id', $this->tableName, 'user_id', '{{%user}}', 'id', 'cascade', 'cascade');
        }
    }

    public function safeDown()
    {
        if (!empty($this->db->getTableSchema($this->tableName))) {
            $this->dropForeignKey('fk_user_id', $this->tableName);
            $this->dropTable($this->tableName);
        }
    }
}
