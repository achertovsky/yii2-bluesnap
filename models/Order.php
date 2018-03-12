<?php

namespace achertovsky\bluesnap\models;

use Yii;
use achertovsky\bluesnap\models\Core;
use achertovsky\bluesnap\models\Shopper;
use achertovsky\bluesnap\models\Sku;

/**
 * This is the model class for table "bluesnap_cart".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $shopper_id
 * @property integer $sku_id
 * @property integer $status
 * @property integer $quantity
 *
 * @property Shopper $shopper
 * @property Sku $sku
 */
class Order extends Core
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bluesnap_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'shopper_id', 'sku_id', 'status', 'quantity'], 'integer'],
            [['shopper_id'], 'exist', 'skipOnError' => false, 'targetClass' => Shopper::className(), 'targetAttribute' => ['shopper_id' => 'shopper_id']],
            [['sku_id'], 'exist', 'skipOnError' => false, 'targetClass' => Sku::className(), 'targetAttribute' => ['sku_id' => 'sku_id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopper()
    {
        return $this->hasOne(BluesnapShopper::className(), ['shopper_id' => 'shopper_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSku()
    {
        return $this->hasOne(BluesnapSku::className(), ['sku_id' => 'sku_id']);
    }
}
