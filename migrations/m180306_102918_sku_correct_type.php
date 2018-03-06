<?php

use yii\db\Migration;

class m180306_102918_sku_correct_type extends Migration
{
    public $tableName = '{{%bluesnap_sku}}';
    
    public function safeUp()
    {
        if ($this->db->getTableSchema($this->tableName)->getColumn('contract_name')) {
            $this->alterColumn($this->tableName, 'contract_name', $this->string());
        }
    }

    public function safeDown()
    {
        echo "m180306_102918_sku_correct_type cannot be reverted.\n";
    }
}
