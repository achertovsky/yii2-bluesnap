<?php

use yii\db\Migration;

class m180403_120416_order_usd_amount extends Migration
{
    public $tableName = '{{%bluesnap_order}}';
    public function safeUp()
    {
        if (!$this->db->getTableSchema($this->tableName)->getColumn('usd_amount')) {
            $this->addColumn($this->tableName, 'usd_amount', $this->decimal(12,2));
        }
    }

    public function safeDown()
    {
        if ($this->db->getTableSchema($this->tableName)->getColumn('usd_amount')) {
            $this->dropColumn($this->tableName, 'usd_amount');
        }
    }
}
