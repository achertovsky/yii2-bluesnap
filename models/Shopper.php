<?php

namespace backend\models;

use Yii;
use achertovsky\bluesnap\models\Core;
use achertovsky\bluesnap\models\ShopperInfo;

/**
 * This is the model class for table "bluesnap_shopper".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 * @property integer $shopper_id
 * @property string $web_info
 * @property string $fraud_info
 * @property string $shopper_info
 * @property integer $wallet_id
 */
class Shopper extends Core
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'bluesnap_shopper';
    }
    
    /**
     * List of urls for api requests
     * @var string
     */
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/shoppers';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/shoppers';

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'wallet_id', 'user_id', 'shopper_id'], 'integer'],
            [['web_info', 'fraud_info', 'shopper_info'], 'string'],
            [['web_info', 'fraud_info', 'shopper_info', 'user_id', 'shopper_id'], 'required'],
        ];
    }
    
    /** @inheritdoc */
    public $jsonFields = ['web_info', 'fraud_info', 'shopper_info'];
    
    /**
     * @param string $ip
     * Shopper's IP address.
     * @param string $remoteHost
     * Shopper's remote host.
     * @param string $userAgent
     * Shopper's browser info (for web users only).
     */
    public function setWebInfo($ip, $remoteHost, $userAgent)
    {
        $this->web_info = [
            'ip' => $ip,
            'remote_host' => $remoteHost,
            'user_agent' => $userAgent,
        ];
    }
    
    /**
     * @param string $id
     * Unique ID of the shopper whose device fingerprint information was collected on the checkout page.
     * The Fraud Session ID should contain up to 32 alpha-numeric characters only.
     * For setup info, see https://developers.bluesnap.com/docs/fraud-prevention#section-device-data-checks
     * @param string $enterpriseSiteId
     * Site ID configured in Kount. For more information, see https://developers.bluesnap.com/docs/fraud-prevention#section-site-ids
     * @param string $udfName
     * Name of user-defined field.
     * @param string $udfValue
     * Value of user-defined field.
     */
    public function setFraudInfo($id, $enterpriseSiteId = null, $udfName = null, $udfValue = null)
    {
        $this->fraud_info = [
            'fraud_session_id' => md5($id.$this->module->antiFraudSalt),
        ];
        if (!is_null($enterpriseSiteId)) {
            $this->fraudInfo['enterprise_site_id'] = $enterpriseSiteId;
        }
        if (!is_null($udfName) && !is_null($udfValue)) {
            $this->fraudInfo['enterprise_udfs'] = [
                'udf_name' => $udfName,
                'udf_value' => $udfValue,
            ];
        }
    }
    
    /**
     * Defines shopper info
     * @param ShopperInfo $shopper
     * @param integer $storeId
     */
    public function setShopperInfo(ShopperInfo $shopper, $storeId = null)
    {
        $this->shopper_info = $shopper->getData();
        $this->shopper_info['store_id'] = is_null($storeId) ? $this->module->defaultStoreId : $storeId;
    }
    
    /**
     * @return ShopperInfo
     */
    public function getShopperInfo()
    {
        return new ShopperInfo($this->shopper_info);
    }
}
