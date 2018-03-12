<?php

namespace achertovsky\bluesnap;

use yii\base\InvalidConfigException;
use achertovsky\bluesnap\models\Product;
use Yii;
use achertovsky\bluesnap\models\Sku;
use achertovsky\bluesnap\models\Shopper;
use achertovsky\bluesnap\models\Encrypt;

/**
 * Component contains all required actions
 *
 * @author alexander
 */
class Bluesnap extends \yii\base\Object
{
    /**
     * Name of module. It should be identical to name in config `modules` section
     * @var string 
     */
    public $moduleName = 'bluesnap';
    
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        if (empty(Yii::$app->getModule($this->moduleName))) {
            throw new InvalidConfigException("No {$this->moduleName} found in project modules config");
        }
    }
    
    /**
     * Alias for getCommon
     * @param array $where
     * @param string $indexBy
     * @return Product
     * Array of Product
     */
    public function getProductModel($where = [], $indexBy = 'product_id')
    {
        return $this->getCommon(Product::className(), $where, $indexBy);
    }
    
    /**
     * Alias for getCommon
     * @param array $where
     * @param string $indexBy
     * @return Sku
     * Array of Sku
     */
    public function getSkuModel($where = [], $indexBy = 'product_id')
    {
        return $this->getCommon(Sku::className(), $where, $indexBy);
    }
    
    /**
     * Alias for getCommon
     * @param array $where
     * @param string $indexBy
     * @return Shopper
     * Array of Shopper
     */
    public function getShopperModel($where = [], $indexBy = 'id')
    {
        return $this->getCommon(Shopper::className(), $where, $indexBy);
    }
    
    /**
     * Creates new or returns list by filter
     * @param string $className
     * @param array $where
     * @param string $indexBy
     * @return array of $className
     */
    public function getCommon($className, $where = [], $indexBy = 'product_id')
    {
        if (empty($where)) {
            $model = new $className();
            $model->module = Yii::$app->getModule($this->moduleName);
            $model->setUrl();
            return $model;
        }
        $models = $className::find()->where($where)->indexBy($indexBy)->all();
        foreach ($models as $key => $model) {
            $model->module = Yii::$app->getModule($this->moduleName);
            $model->setUrl();
            $models[$key] = $model;
        }
        return $models;
    }
    
    /**
     * @var Encrypt|null 
     */
    protected $enc = null;
    
    /**
     * @return Encrypt
     */
    protected function getEncrypt()
    {
        if (is_null($this->enc)) {
            $this->enc = new Encrypt();
            $this->enc->module = Yii::$app->getModule($this->moduleName);
            $this->enc->setUrl();
        }
        return $this->enc;
    }
    
    /**
     * Alias for Encrypt
     * Preinits requirements of Encrypt
     * @param array $params
     */
    public function encryptParams($params)
    {
        $enc = $this->encrypt;
        return $enc->encryptParams($params);
    }
    
    /**
     * Alias for Encrypt
     * Preinits requirements of Encrypt
     * @param string $encryptedToken
     */
    public function decryptParams($encryptedToken)
    {
        $enc = $this->encrypt;
        return $enc->decryptParams($encryptedToken);
    }
}
