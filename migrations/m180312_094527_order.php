<?php

use yii\db\Migration;

class m180312_094527_order extends Migration
{
    public $tableName = '{{%bluesnap_order}}';
    public function safeUp()
    {
        if (empty($this->db->getTableSchema($this->tableName))) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'created_at' => $this->integer(),
                    'updated_at' => $this->integer(),
                    'shopper_id' => $this->integer(),
                    'sku_id' => $this->integer(),
                    'status' => $this->smallInteger(),
                    'quantity' => $this->integer(),
                ]
            );
            $this->addForeignKey('fk_shopper_id', $this->tableName, 'shopper_id', '{{%bluesnap_shopper}}', 'shopper_id', 'cascade', 'cascade');
            $this->addForeignKey('fk_sku_id', $this->tableName, 'sku_id', '{{%bluesnap_sku}}', 'sku_id', 'cascade', 'cascade');
            $this->createIndex('i_shopper_id', $this->tableName, 'shopper_id');
            $this->createIndex('i_sku_id', $this->tableName, 'sku_id');
        }
    }

    public function safeDown()
    {
        if (!empty($this->db->getTableSchema($this->tableName))) {
            $this->dropForeignKey('fk_shopper_id', $this->tableName);
            $this->dropForeignKey('fk_sku_id', $this->tableName);
            $this->dropIndex('i_sku_id', $this->tableName);
            $this->dropIndex('i_shopper_id', $this->tableName);
            $this->dropTable($this->tableName);
        }
    }
}
