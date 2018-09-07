<?php

use yii\db\Migration;

/**
 * Class m180907_103739_order_ref_num
 */
class m180907_103739_order_ref_num extends Migration
{
    public $tableName = '{{%bluesnap_order}}';
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        if (!$this->db->getTableSchema($this->tableName)->getColumn('reference_number')) {
            $this->addColumn($this->tableName, 'reference_number', $this->string());
        }
    }

    public function down()
    {
        if ($this->db->getTableSchema($this->tableName)->getColumn('reference_number')) {
            $this->dropColumn($this->tableName, 'reference_number');
        }
    }
}
