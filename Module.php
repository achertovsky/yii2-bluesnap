<?php

namespace achertovsky\bluesnap;

use yii\base\InvalidConfigException;
use Yii;

/**
 * @author alexander
 */
class Module extends \yii\base\Module
{
    /**
     * Token of application
     * @var string
     */
    public $username = '';
    public $password = '';
    public $defaultStoreId = '';
    public $clientSideEncryptionKey = '';
    public $authToken = '';
    public $antiFraudSalt = '';
    public $sellerId = '';
    public $dataProtectionKey = '';
    public $backToSellerUrl = '';
    
    /**
     * Number of minutes that the pre-populated checkout page will be available; maximum is 1440 minutes (i.e. 24 hours)
     * @var int
     */
    public $expirationInMinutes = 30;
    
    /**
     * Determine if use sandbox or not
     * @var bool
     */
    public $sandbox = true;
    
    /**
     * Additional checks of params inside
     * @throws InvalidConfigException
     */
    public function init()
    {
        Yii::setAlias('@achertovsky/bluesnap/controllers', __DIR__.DIRECTORY_SEPARATOR.'controllers');
        parent::init();
        if (!empty($this->username) && !empty($this->password)) {
            $this->authToken = "Basic ".base64_encode($this->username.':'.$this->password);
        }
        $paramsToCheck = ['authToken', 'defaultStoreId', 'clientSideEncryptionKey', 'antiFraudSalt', 'sellerId'];
        foreach ($paramsToCheck as $param) {
            if (empty($this->$param)) {
                throw new InvalidConfigException("No value for $param param provided");
            }
        }
    }
}
