<?php

use yii\db\Migration;

class m180309_104053_shopper_unique_fields extends Migration
{
    public function safeUp()
    {
        try {
            if (!empty($this->db->getTableSchema('{{%bluesnap_shopper}}')->getColumn('user_id'))) {
                $this->createIndex('i_user_id_unique', '{{%bluesnap_shopper}}', 'user_id', true);
            }
        } catch (\Exception $ex) {
            echo "seem index i_user_id_unique already exist\n";
        }
        try {
            if (!empty($this->db->getTableSchema('{{%bluesnap_shopper}}')->getColumn('shopper_id'))) {
                $this->createIndex('i_shopper_id_unique', '{{%bluesnap_shopper}}', 'shopper_id', true);
            }
        } catch (\Exception $ex) {
            echo "seem index i_shopper_id_unique already exist\n";
        }
    }

    public function safeDown()
    {
        echo "m180309_104053_shopper_unique_fields cannot be reverted.\n";
    }
}
