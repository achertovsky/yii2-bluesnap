<?php

use yii\db\Migration;

class m180227_095345_product extends Migration
{
    private $tableName = '{{%bluesnap_product}}';
    
    public function up()
    {
        if (!$this->db->getTableSchema($this->tableName)) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'created_at' => $this->integer(),
                    'updated_at' => $this->integer(),
                    'product_id' => $this->integer(),
                    'product_status' => $this->string(1),
                    'product_name' => $this->string(),
                    'product_short_description' => $this->text(),
                    'product_long_description' => $this->text(),
                    'product_info_url' => $this->text(),
                    'product_image' => $this->text(),
                    'product_merchant_descriptor' => $this->text(),
                    'product_support_email' => $this->string(254),
                ]
            );
        }
    }

    public function down()
    {
        if ($this->db->getTableSchema($this->tableName)) {
            $this->dropTable($this->tableName);
        }
    }
}
