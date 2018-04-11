<?php

namespace achertovsky\bluesnap\models;

use Yii;
use achertovsky\bluesnap\models\Core;
use achertovsky\bluesnap\models\Shopper;
use achertovsky\bluesnap\models\Sku;
use achertovsky\bluesnap\models\Product;
use achertovsky\bluesnap\models\Subscription;
use achertovsky\bluesnap\models\PricingSettings;
use achertovsky\bluesnap\helpers\Request;
use achertovsky\bluesnap\helpers\Xml;

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
 * @property integer $subscription_id
 * @property double $usd_amount
 * @property array $ipnPost
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
    const STATUS_COMPLETED = 1;
    const STATUS_CANCELLED = 2;
    
    /**
     * To use, lets say, in dropdown
     */
    const STATUSES_ARRAY = [
        1 => 'Completed',
        2 => 'Cancelled',
    ];
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['created_at', 'updated_at', 'shopper_id', 'sku_id', 'status', 'quantity', 'product_id', 'subscription_id'], 'integer'],
            [['shopper_id'], 'exist', 'skipOnError' => false, 'targetClass' => Shopper::className(), 'targetAttribute' => ['shopper_id' => 'shopper_id']],
            [['sku_id'], 'exist', 'skipOnError' => false, 'targetClass' => Sku::className(), 'targetAttribute' => ['sku_id' => 'sku_id']],
            [['product_id'], 'exist', 'skipOnError' => false, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
            [['usd_amount']],
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
     * @var Subscription
     */
    private $sub = null;
    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        if (is_null($this->sub)) {
            $this->sub = Yii::$app->bluesnap->subscriptionModel;
        }
        return $this->sub;
    }
    
    /**
     * 
     * @param type $skuId
     * @return boolean
     */
    public function changeContract($skuId)
    {
        if (empty($this->subscription_id)) {
            return false;
        }
        /* @var $subscription Subscription */
        $subscription = $this->subscription;
        return $subscription->switchSubscriptionContract($this->subscription_id, $skuId, $this->shopper_id);
    }
    
    /**
     * @param int $statusId
     * @return string
     */
    public static function getStatusName($statusId)
    {
        switch ($statusId) {
            case self::STATUS_COMPLETED:
                return 'Completed';
            case self::STATUS_CANCELLED:
                return 'Cancelled';
        }
    }
    
    /**
     * Places an order for existing user
     * @param int $shopperId
     * @param int $skuId
     * @param int $quantity
     * @return boolean
     */
    public function createOrderWithExistingShopper($shopperId, $skuId, $quantity = 1)
    {
        /* @var $sku Sku */
        $sku = Yii::$app->bluesnap->getSkuModel(
            [
                'sku_id' => $skuId,
            ],
            null,
            true
        );
        /* @var $shopper Shopper */
        $shopper = Yii::$app->bluesnap->getShopperModel(
            [
                'shopper_id' => $shopperId,
            ],
            null,
            true
        );
        if (empty($shopper) || empty($sku)) {
            return false;
        }
        /* @var $pricingSettings PricingSettings */
        $pricingSettings = $sku->pricingSettings;
        $body = Xml::prepareBody(
            'order',
            [
                'ordering_shopper' => [
                    'shopper_id' => $shopperId,
                    'web_info' => $shopper->web_info,
                    'fraud_info' => $shopper->fraud_info,
                    'authorized_by_shopper' => true,
                ],
                'cart' => [
                    'cart_item' => [
                        'sku' => [
                            'sku_id' => $skuId,
                        ],
                        'quantity' => $quantity,
                    ],
                ],
                'expected-total-price' => [
                    'currency' => $pricingSettings->getCurrency(),
                    'amount' => $pricingSettings->getPrice(),
                ],
            ]
        );
        $response = Request::post(
            $this->module->sandbox ? "https://sandbox.bluesnap.com/services/2/orders" : "https://ws.bluesnap.com/services/2/orders",
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        
        $code = $response->getStatusCode();
        //docs says 201 - success
        if (in_array($code, [201, 200])) {
            return true;
        }
        $content = Xml::parse($response->getContent());
        Yii::error(var_export($content, true));
        return false;
    }
    
    /**
     * @var array 
     */
    public $ipnPost = [];
}
