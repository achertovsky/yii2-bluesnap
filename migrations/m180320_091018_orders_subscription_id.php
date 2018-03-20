<?php

use yii\db\Migration;
use achertovsky\bluesnap\models\PricingSettings;
use yii\helpers\Json;
use achertovsky\bluesnap\models\Order;

class m180320_091018_orders_subscription_id extends Migration
{
    public $tableName = '{{%bluesnap_order}}';
    public function safeUp()
    {
        if (!$this->db->getTableSchema($this->tableName)->getColumn('subscription_id')) {
            $this->addColumn($this->tableName, 'subscription_id', $this->integer());
        }
    }

    public function safeDown()
    {
        if ($this->db->getTableSchema($this->tableName)->getColumn('subscription_id')) {
            $this->dropColumn($this->tableName, 'subscription_id');
        }
    }
}
