<?php

namespace achertovsky\bluesnap\models;

use Yii;
use achertovsky\bluesnap\models\Core;
use achertovsky\bluesnap\helpers\Request;
use achertovsky\bluesnap\helpers\Xml;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bluesnap_sku".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $contract_name
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
 * @property bool $default
 */
class Sku extends Core
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
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/catalog/skus';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/catalog/skus';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bluesnap_sku';
    }

    /** @inheritdoc */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'create' => ['product_id', 'sku_type', 'pricing_settings']
            ]
        );
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sku_id'], 'unique'],
            [['product_id', 'sku_type', 'pricing_settings', 'sku_id'], 'required'],
            [['created_at', 'updated_at', 'product_id', 'sku_id'], 'integer'],
            [['pricing_settings', 'contract_name', 'sku_image', 'sku_quantity_policy', 'sku_effective_dates', 'sku_coupon_settings', 'sku_custom_parameters'], 'string'],
            [['sku_status'], 'string', 'max' => 1],
            [['sku_type'], 'string', 'max' => 255],
            [['default', 'collect_shipping_address'], 'boolean'],
        ];
    }

    /**
     * Amount of recursive calls in case of general error
     * @var int 
     */
    public $getDepth = 0;
    
    /**
     * Receives SKU object and saves it to db
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/retrieve-sku
     * @return Sku
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
        if (isset($response['messages']['message']['code']) &&
            $response['messages']['message']['code'] == 10000 &&
            $this->getDepth < 10
        ) {
            $this->getDepth++;
            return $this->getSku();
        } elseif ($this->getDepth >= 10) {
            return null;
        }
        $this->setAttributes($response['catalog_sku']);
        if ($this->save()) {
            $this->getDepth = 0;
            return $this;
        }
        $this->getDepth = 0;
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public $jsonFields = ['pricing_settings', 'sku_quantity_policy', 'sku_effective_dates', 'sku_coupon_settings', 'sku_custom_parameters'];
    
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
    
    /**
     * Updates SKU
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/update-sku
     * @param bool $save
     * @return boolean|\achertovsky\bluesnap\models\Sku
     */
    public function updateSku($save = true)
    {
        if (!$this->validate()) {
            return false;
        }
        $body = Xml::prepareBody('catalog_sku', $this->getAttributes());
        $response = Request::put(
            $this->url.'/'.$this->sku_id,
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        //docs says 204 - success
        if ($code == 204) {
            if (!$save) {
                return $this;
            }
            if ($this->save()) {
                return $this;
            } 
        }
        return false;
    }
    
    /**
     * Deletes SKU
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/update-sku
     * @param bool $fromDb
     * @return boolean|\achertovsky\bluesnap\models\Sku
     */
    public function deleteSku($fromDb = false)
    {
        $this->sku_status = self::SKU_STATUS_DELETED;
        $sku = $this->updateSku(false);
        if (empty($sku)) {
            return $sku;
        }
        if ($fromDb) {
            $sku->delete();
        } else {
            $sku->save();
        }
        return $sku;
    }
    
    /**
     * Creates SKU
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/create-sku
     * @param string $type
     * @return boolean|\achertovsky\bluesnap\models\Sku
     */
    public function createSku($type = self::SKU_TYPE_DIGITAL)
    {
        $this->sku_type = $type;
        $this->scenario = 'create';
        if (!$this->validate()) {
            Yii::error("Validation error(s):".var_export($this->getErrors(), true));
            return false;
        }
        $this->scenario = 'default';
        $body = Xml::prepareBody('catalog-sku', $this->getAttributes());
        $response = Request::post(
            $this->url,
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        $possibleErrors = Xml::parse($response->getContent());
        if (!empty($possibleErrors)) {
            Yii::error("Error occured on creation:".var_export($possibleErrors, true));
        }
        //docs says 201 - success
        if ($code == 201) {
            //get sku id from response
            $headers = $response->getHeaders();
            if (isset($headers['location'])) {
                $location = $headers['location'];
                $this->sku_id = substr($location, strrpos($location, '/')+1);
            }
            $this->isNewRecord = true;
            $this->id = null;
            if ($this->save()) {
                return $this;
            } 
        }
        return false;
    }
    
    /**
     * @param decimal $amount
     * @param string $currency
     * @param bool $basePrice
     */
    public function prepareOneTimePayment($amount, $currency = 'USD', $basePrice = true)
    {
        $this->pricing_settings = (new PricingSettings)->getOneTimePayment($amount, $currency, $basePrice);
    }
    
    /**
     * @param decimal $amount
     * @param int $trialLenght
     * @param string $currency
     * @param string $trialInterval
     * @param bool $basePrice
     */
    public function prepareOneTimePaymentWithTrial($amount, $trialLenght, $currency = 'USD', $trialInterval = ChargePolicy::INTERVAL_DAYS, $basePrice = true)
    {
        $this->pricing_settings = (new PricingSettings)->getOneTimePaymentWithTrial($amount, $trialLenght, $currency, $trialInterval, $basePrice);
    }
    
    /**
     * @param decimal $amount
     * @param int $periodFrequency
     * PricingSettings has constants for it
     * @param string $currency
     * @param bool $basePrice
     */
    public function prepareSubscription($amount, $periodFrequency, $currency = 'USD', $basePrice = true)
    {
        $this->pricing_settings = (new PricingSettings)->getSubscription($amount, $periodFrequency, $currency, $basePrice);
    }
    
    /**
     * @param decimal $amount
     * @param int $periodFrequency
     * PricingSettings has constants for it
     * @param int $trialLenght
     * @param string $currency
     * @param string $trialInterval
     * @param bool $basePrice
     */
    public function prepareSubscriptionWithTrial($amount, $periodFrequency, $trialLenght, $currency = 'USD', $trialInterval = ChargePolicy::INTERVAL_DAYS, $basePrice = true)
    {
        $this->pricing_settings = (new PricingSettings)->getSubscriptionWithTrial($amount, $periodFrequency, $trialLenght, $currency, $trialInterval, $basePrice);
    }
    
    /**
     * @param decimal $amount
     * @param int $periodFrequency
     * PricingSettings has constants for it
     * @param int $initialPeriod
     * @param decimal $initialAmount
     * @param string $currency
     * @param string $initialInterval
     * @param bool $basePrice
     */
    public function prepareSubscriptionWithInitialCharge($amount, $periodFrequency, $initialPeriod, $initialAmount, $currency = 'USD', $initialInterval = ChargePolicy::INTERVAL_DAYS, $basePrice = true)
    {
        $this->pricing_settings = (new PricingSettings)->getSubscriptionWithInitialCharge($amount, $periodFrequency, $initialPeriod, $initialAmount, $currency, $initialInterval, $basePrice);
    }
    
    /**
     * Making sure only 1 default exist
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isAttributeChanged('default') && $this->default) {
                Sku::updateAll(
                    [
                        'default' => false,
                    ],
                    [
                        'and',
                        ['=', 'product_id', $this->product_id],
                        ['!=', 'id', $this->id],
                    ]
                );
            }
            return true;
        }
        return false;
    }
    
    /**
     * On every update/create make sure that sku ipn enabled
     * @var array
     */
    public $sku_ipn_settings = [
        'use_seller_level_settings' => true,
    ];
    
    /**
     * Also attach custom attributes
     * @inheritdoc
     */
    public function attributes()
    {
        return ArrayHelper::merge(
            parent::attributes(),
            [
                'sku_ipn_settings',
            ]
        );
    }
    
    /**
     * @return \achertovsky\bluesnap\models\PricingSettings
     */
    public function getPricingSettings()
    {
        $pricingSettings = new PricingSettings($this->pricing_settings);
        return $pricingSettings;
    }
}
