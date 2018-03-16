<?php

namespace achertovsky\bluesnap\models;

use Yii;
use achertovsky\bluesnap\models\Core;
use achertovsky\bluesnap\models\Shopper;
use achertovsky\bluesnap\models\Sku;
use achertovsky\bluesnap\models\Product;
use yii\base\Event;

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
 * @property integer $product_id
 *
 * @property Shopper $shopper
 * @property Sku $sku
 * @property Product $product
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
     * List of possible status codes
     */
    const STATUS_CREATED = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_CANCELLED = 2;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['created_at', 'updated_at', 'shopper_id', 'sku_id', 'status', 'quantity', 'product_id'], 'integer'],
            [['shopper_id'], 'exist', 'skipOnError' => false, 'targetClass' => Shopper::className(), 'targetAttribute' => ['shopper_id' => 'shopper_id']],
            [['sku_id'], 'exist', 'skipOnError' => false, 'targetClass' => Sku::className(), 'targetAttribute' => ['sku_id' => 'sku_id']],
            [['product_id'], 'exist', 'skipOnError' => false, 'targetClass' => Sku::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopper()
    {
        return $this->hasOne(Shopper::className(), ['shopper_id' => 'shopper_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSku()
    {
        return $this->hasOne(Sku::className(), ['sku_id' => 'sku_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
    
    /**
     * List of eveents
     */
    const EVENT_ORDER_CREATED = 'bluesnap_order_created';
    const EVENT_ORDER_UPDATED = 'bluesnap_order_updated';
    
    /**
     * Events triggering
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $event = new Event();
        $event->sender = $this;
        if ($insert) {
            Event::trigger($this->className(), self::EVENT_ORDER_CREATED, $event);
        } else {
            Event::trigger($this->className(), self::EVENT_ORDER_UPDATED, $event);
        }
    }
}
