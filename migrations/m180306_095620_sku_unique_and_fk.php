<?php

use yii\db\Migration;

class m180306_095620_sku_unique_and_fk extends Migration
{
    public $tableName = '{{%bluesnap_sku}}';
    public function safeUp()
    {
        if ($this->db->getTableSchema($this->tableName)->getColumn('sku_id')) {
            $this->createIndex('unique_sku_id', $this->tableName, 'sku_id', true);
        }
        if ($this->db->getTableSchema($this->tableName)->getColumn('product_id')) {
            $this->addForeignKey('fk_product_id', $this->tableName, 'product_id', '{{%bluesnap_product}}', 'product_id', 'cascade', 'cascade');
        }
    }

    public function safeDown()
    {
        echo "m180306_095620_sku_unique_and_fk cannot be reverted.\n";
    }
}
