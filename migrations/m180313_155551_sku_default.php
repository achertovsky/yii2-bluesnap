<?php

use yii\db\Migration;

class m180313_155551_sku_default extends Migration
{
    public $tableName = '{{%bluesnap_sku}}';
    public function safeUp()
    {
        if ($this->db->getTableSchema($this->tableName)->getColumn('default')) {
            $this->addColumn($this->tableName, 'default', $this->boolean()->defaultValue(false));
        }
    }

    public function safeDown()
    {
        echo "m180313_155551_sku_default cannot be reverted.\n";
    }
}
