<?php

namespace achertovsky\bluesnap;

use yii\base\InvalidConfigException;
use achertovsky\bluesnap\models\Product;
use Yii;
use achertovsky\bluesnap\models\Sku;
use achertovsky\bluesnap\models\Shopper;
use achertovsky\bluesnap\models\Encrypt;
use achertovsky\bluesnap\models\Cart;
use achertovsky\bluesnap\helpers\IPN;
use achertovsky\bluesnap\models\Order;
use achertovsky\bluesnap\models\Subscription;
use achertovsky\bluesnap\helpers\HostedPaymentFields;
use achertovsky\bluesnap\models\Report;

/**
 * Component contains all required actions
 *
 * @author alexander
 */
class Bluesnap extends \yii\base\BaseObject
{
    /**
     * Gonna contain map to classes.
     * If class empty - will use default
     * @var array
     */
    public $modelMap = [];
    
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
     * @param bool $single
     * Return one item or array (by default array)
     * @return Product[]|Product
     */
    public function getProductModel($where = [], $indexBy = 'product_id', $single = false)
    {
        $array = $this->getCommon(
            isset($this->modelMap['product']) ? $this->modelMap['product'] : Product::className(),
            $where,
            $indexBy
        );
        if ($single) {
            if (empty($array)) {
                return null;
            }
            return reset($array);
        }
        return $array;
    }
    
    /**
     * Alias for getCommon
     * @param array $where
     * @param string $indexBy
     * @param bool $single
     * Return one item or array (by default array)
     * @return Sku[]|Sku
     */
    public function getSkuModel($where = [], $indexBy = 'sku_id', $single = false)
    {
        $array = $this->getCommon(
            isset($this->modelMap['sku']) ? $this->modelMap['sku'] : Sku::className(),
            $where,
            $indexBy
        );
        if ($single) {
            if (empty($array)) {
                return null;
            }
            return reset($array);
        }
        return $array;
    }
    
    /**
     * Alias for getCommon
     * @param array $where
     * @param string $indexBy
     * @param bool $single
     * Return one item or array (by default array)
     * @return Shopper[]|Shopper
     */
    public function getShopperModel($where = [], $indexBy = 'id', $single = false)
    {
        $array = $this->getCommon(
            isset($this->modelMap['shopper']) ? $this->modelMap['shopper'] : Shopper::className(),
            $where,
            $indexBy
        );
        if ($single) {
            if (empty($array)) {
                return null;
            }
            return reset($array);
        }
        return $array;
    }
    
    /**
     * Creates new or returns list by filter
     * @param string $className
     * @param array $where
     * @param string $indexBy
     * @return array of $className
     */
    public function getCommon($className, $where = [], $indexBy = 'product_id', $doSetUrl = true)
    {
        if (empty($where)) {
            $model = new $className();
            $model->module = Yii::$app->getModule($this->moduleName);
            if ($doSetUrl) {
                $model->setUrl();
            }
            return $model;
        }
        $models = $className::find()->where($where)->indexBy($indexBy)->all();
        foreach ($models as $key => $model) {
            $model->module = Yii::$app->getModule($this->moduleName);
            if ($doSetUrl) {
                $model->setUrl();
            }
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
    
    /**
     * @return Cart
     */
    public function getCart()
    {
        if (isset($this->modelMap['cart'])) {
            $cart = new $this->modelMap['cart'];
        } else {
            $cart = new Cart();
        }
        $cart->module = Yii::$app->getModule($this->moduleName);
        $cart->setUrl();
        return $cart;
    }
    
    /**
     * Alias for IPN::handleIpn
     * @return mixed
     */
    public function handleIpn()
    {
        $ipn = new IPN;
        $ipn->module = Yii::$app->getModule($this->moduleName);
        $ipn->setIps();
        return $ipn->handleIpn();
    }
    
    /**
     * Alias for getCommon
     * @param array $where
     * @param string $indexBy
     * @param bool $single
     * Return one item or array (by default array)
     * @return Order[]|Order
     */
    public function getOrderModel($where = [], $indexBy = 'id', $single = false)
    {
        $array = $this->getCommon(
            isset($this->modelMap['order']) ? $this->modelMap['order'] : Order::className(),
            $where,
            $indexBy,
            false
        );
        if ($single) {
            if (empty($array)) {
                return null;
            }
            return reset($array);
        }
        return $array;
    }
    
    /**
     * @return Subscription
     */
    public function getSubscriptionModel()
    {
        if (isset($this->modelMap['subscription'])) {
            $subscription = new $this->modelMap['subscription'];
        } else {
            $subscription = new Subscription();
        }
        $subscription->module = Yii::$app->getModule($this->moduleName);
        $subscription->setUrl();
        return $subscription;
    }

    /**
     * @return HostedPaymentFields
     */
    public function getHostedPaymentFieldsHelper()
    {
        $helper = new HostedPaymentFields(
            [
                'module' => Yii::$app->getModule($this->moduleName),
            ]
        );
        $helper->setUrl();
        return $helper;
    }

    /**
     * @return Report
     */
    public function getReportModel()
    {
        if (isset($this->modelMap['report'])) {
            $report = new $this->modelMap['report'];
        } else {
            $report = new Report();
        }
        $report->module = Yii::$app->getModule($this->moduleName);
        $report->setUrl();
        return $report
    }
}
