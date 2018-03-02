<?php

namespace achertovsky\bluesnap\models;

use Yii;

/**
 * This is the model class for table "bluesnap_sku".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $contract_name
 * @property integer $product_id
 * @property string $sku_status
 * @property string $sku_type
 * @property string $pricing_settings
 * @property string $sku_image
 * @property string $sku_quantity_policy
 * @property integer $collect_shipping_address
 * @property string $sku_effective_dates
 * @property string $sku_coupon_settings
 * @property string $sku_custom_parameters
 */
class Sku extends \yii\db\ActiveRecord
{
    /**
     * List of possible sku status codes
     */
    const SKU_STATUS_ACTIVE = 'A';
    const SKU_STATUS_INACTIVE = 'I';
    const SKU_STATUS_DELETED = 'D';
    
    /**
     * List of possible sku types
     */
    const SKU_TYPE_PHISICAL = 'PHYSICAL';
    const SKU_TYPE_DIGITAL = 'DIGITAL';
    
    /**
     * List of urls for api requests
     * @var string
     */
    protected $url = '';
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/catalog/skus';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/catalog/skus';
    
    /** @inheritdoc */
    public function setUrl()
    {
        if ($this->module->sandbox) {
            $this->url = $this->sandboxUrl;
        } else {
            $this->url = $this->liveUrl;
        }
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bluesnap_sku';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'sku_type', 'pricing_settings'], 'required'],
            [['created_at', 'updated_at', 'contract_name', 'product_id', 'collect_shipping_address'], 'integer'],
            [['pricing_settings', 'sku_image', 'sku_quantity_policy', 'sku_effective_dates', 'sku_coupon_settings', 'sku_custom_parameters'], 'string'],
            [['sku_status'], 'string', 'max' => 1],
            [['sku_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'contract_name' => 'Contract Name',
            'product_id' => 'Product ID',
            'sku_status' => 'Sku Status',
            'sku_type' => 'Sku Type',
            'pricing_settings' => 'Pricing Settings',
            'sku_image' => 'Sku Image',
            'sku_quantity_policy' => 'Sku Quantity Policy',
            'collect_shipping_address' => 'Collect Shipping Address',
            'sku_effective_dates' => 'Sku Effective Dates',
            'sku_coupon_settings' => 'Sku Coupon Settings',
            'sku_custom_parameters' => 'Sku Custom Parameters',
        ];
    }
    
    /**
     * @param string $productId
     * @param string $skuType
     * @param string $pricingSettings
     */
    public function defineMinimalRequirements($productId, $skuType, $pricingSettings)
    {
        $this->product_id = $productId;
        $this->sku_type = $skuType;
        $this->pricing_settings = $pricingSettings;
    }
    
    public function updateSku()
    {
        
    }

}
