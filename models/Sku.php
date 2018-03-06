<?php

namespace achertovsky\bluesnap\models;

use Yii;
use achertovsky\bluesnap\models\Core;
use achertovsky\bluesnap\helpers\Request;
use achertovsky\bluesnap\helpers\Xml;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

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
 * @property integer $sku_id
 */
class Sku extends Core
{
    /** @inheritdoc */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    'class' => TimestampBehavior::className(),
                ],
            ]
        );
    }
    
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
     * List of possible coupons
     */
    const SKU_COUPON_ENABLE = "ENABLE";
    const SKU_COUPON_DISABLE = "DISABLE";
    const SKU_COUPON_ENABLE_MANDATORY = "ENABLE_MANDATORY";
    const SKU_COUPON_PARAMETER_ONLY = "PARAMETER_ONLY";
    
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
            [['sku_id'], 'unique'],
            [['product_id', 'sku_type', 'pricing_settings', 'sku_id'], 'required'],
            [['created_at', 'updated_at', 'product_id', 'collect_shipping_address', 'sku_id'], 'integer'],
            [['pricing_settings', 'contract_name', 'sku_image', 'sku_quantity_policy', 'sku_effective_dates', 'sku_coupon_settings', 'sku_custom_parameters'], 'string'],
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
     * Receives SKU object and saves it to db
     * @return \achertovsky\bluesnap\models\Sku
     */
    public function getSku()
    {
        $content = Request::get(
            $this->url.'/'.$this->sku_id,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        )->getContent();
        $response = Xml::parse($content);
        $this->setAttributes($response['catalog_sku']);
        if ($this->save()) {
            return $this;
        }
        return null;
    }
    
    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (is_array($this->pricing_settings)) {
                $this->pricing_settings = Json::encode($this->pricing_settings);
            }
            return true;
        }
        return false;
    }
    
    /** @inheritdoc */
    public function afterFind()
    {
        parent::afterFind();
        $this->pricing_settings = Json::decode($this->pricing_settings);
    }
    
    /**
     * @param bool $allowQuantityChange
     * Indicates whether it is possible to change the ordered quantity for this SKU.
     * @param int $minimumQuantity
     * Minimum quantity to order for this SKU.
     */
    public function setSkuQuantityPolicy($allowQuantityChange = true, $minimumQuantity = 1)
    {
        $this->sku_quantity_policy = [
            'allow_quantity_change' => $allowQuantityChange,
            'minimum_quantity' => $minimumQuantity,
        ];
    }
    
    /**
     * 
     * @param string $effectiveFrom
     * Effective start date of the SKU.
     * Format: DD-MMM-YY
     * @param string $effectiveTill
     * Effective end date of the SKU.
     * Format: DD-MMM-YY
     */
    public function setSkuEffectiveDates($effectiveFrom, $effectiveTill)
    {
        $this->sku_effective_dates = [
            'effective_from' => $effectiveFrom,
            'effective_till' => $effectiveTill,
        ];
    }
    
    /**
     * @param string $couponSetting
     */
    public function setSkuCouponSettings($couponSetting = self::SKU_COUPON_DISABLE)
    {
        $this->sku_coupon_settings = [
            'sku_coupon_setting' => $couponSetting,
        ];
    }
    
    /**
     * Container of sku-custom-parameter properties.
     * This property may appear more than once within the resource.
     * @param array $parameters
     */
    public function setSkuCustomParameters($parameters)
    {
        $this->sku_custom_parameters = [
            'sku_custom_parameter' => $parameters,
        ];
    }
}
