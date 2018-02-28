<?php

use yii\db\Migration;

class m180228_155610_sku extends Migration
{
    private $tableName = '{{%bluesnap_sku}}';
    
    public function up()
    {
        if (!$this->db->getTableSchema($this->tableName)) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'created_at' => $this->integer(),
                    'updated_at' => $this->integer(),
                    'contract_name' => $this->integer(),
                    'product_id' => $this->integer(),
                    'sku_status' => $this->string(1),
                    'sku_type' => $this->string(),
                    'pricing_settings' => $this->text(),
                    'sku_image' => $this->text(),
                    'sku_quantity_policy' => $this->text(),
                    'collect_shipping_address' => $this->boolean(),
                    'sku_effective_dates' => $this->text(),
                    'sku_coupon_settings' => $this->text(),
                    'sku_custom_parameters' => $this->text(),
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
