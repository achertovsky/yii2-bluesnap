<?php

use yii\db\Migration;

class m180228_095049_unique_index extends Migration
{
    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%bluesnap_product}}')->getColumn('product_id')) {
            $this->createIndex('i_product_id_unique', '{{%bluesnap_product}}', 'product_id');
        }
    }

    public function safeDown()
    {
        echo "m180228_095049_unique_index cannot be reverted.\n";
    }
}
