<?php

namespace achertovsky\bluesnap;

use yii\base\InvalidConfigException;

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
        parent::init();
        if (!empty($this->username) && !empty($this->password)) {
            $this->authToken = "Basic ".base64_encode($this->username.':'.$this->password);
        }
        $paramsToCheck = ['authToken', 'defaultStoreId', 'clientSideEncryptionKey'];
        foreach ($paramsToCheck as $param) {
            if (empty($this->$param)) {
                throw new InvalidConfigException("No value for $param param provided");
            }
        }
    }
}
