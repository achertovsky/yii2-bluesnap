<?php

use yii\db\Migration;

class m180306_100952_fix_product_unique_id extends Migration
{
    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%bluesnap_product}}')->getColumn('product_id')) {
            try {
                $this->dropForeignKey('fk_product_id', '{{%bluesnap_sku}}');
            } catch (\Exception $ex) {
                echo "{$ex->getMessage()}\n";
            }
            try {
                $this->dropIndex('i_product_id_unique', '{{%bluesnap_product}}');
            } catch (\Exception $ex) {
                echo "{$ex->getMessage()}\n";
            }
            try {
                $this->createIndex('i_product_id_unique', '{{%bluesnap_product}}', 'product_id', true);
            } catch (\Exception $ex) {
                echo "{$ex->getMessage()}\n";
            }
            try {
                $this->addForeignKey('fk_product_id', '{{%bluesnap_sku}}', 'product_id', '{{%bluesnap_product}}', 'product_id', 'cascade', 'cascade');
            } catch (\Exception $ex) {
                echo "{$ex->getMessage()}\n";
            }
        }
    }

    public function safeDown()
    {
        echo "m180306_100952_fix_product_unique_id cannot be reverted.\n";
    }
}
