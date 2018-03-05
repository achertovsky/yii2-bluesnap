<?php

use yii\db\Migration;

class m180305_153046_sku_id extends Migration
{
    public $tableName = '{{%bluesnap_sku}}';
    public function safeUp()
    {
        if (empty($this->db->getTableSchema($this->tableName)->getColumn('sku_id'))) {
            $this->addColumn($this->tableName, 'sku_id', $this->integer());
        }
    }

    public function safeDown()
    {
        echo "m180305_153046_sku_id cannot be reverted.\n";
    }
}
