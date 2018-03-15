<?php

use yii\db\Migration;

class m180315_121639_order_product_id extends Migration
{
    public $tableName = '{{%bluesnap_order}}';
    public function safeUp()
    {
        if (empty($this->db->getTableSchema($this->tableName)->getColumn('product_id'))) {
            $this->addColumn($this->tableName, 'product_id', $this->integer());
        }
    }

    public function safeDown()
    {
        echo "m180315_121639_order_product_id cannot be reverted.\n";
    }
}
