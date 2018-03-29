<?php

namespace achertovsky\bluesnap\models;

use yii\base\InvalidConfigException;
use achertovsky\bluesnap\models\Order;
use achertovsky\bluesnap\traits\Common;
use Yii;

/**
 * @property integer $shopperId
 */
class Cart
{
    use Common;
    /**
     * List of urls for api requests
     * @var string
     */
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/buynow/checkout';
    protected $liveUrl = 'https://checkout.bluesnap.com/buynow/checkout';
    
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * Gonna gather items to cart
     * @param int $skuId
     * @param int $quantity
     * @param int $shopperId
     */
    public function addSku($skuId, $quantity)
    {
        $this->data[] = [
            'sku_id' => $skuId,
            'quantity' => $quantity,
        ];
    }
    
    /**
     * Returns formatted buynow link
     * Docs: https://support.bluesnap.com/docs/buynow-parameters
     * @param string $shopperId
     * @param array $parameters
     * Should contain key value according to docs
     * @return string
     * Buynow link
     * @throws InvalidConfigException
     */
    public function processOrder($shopperId, $parameters = [])
    {
        if (empty($this->data)) {
            throw new InvalidConfigException("No skus added");
        }
        $skuIds = [];
        foreach ($this->data as $value) {
            $productId = Sku::find()->where(
                [
                    'sku_id' => $value['sku_id']
                ]
            )->select('product_id')->scalar();
            //check mb order already exist
            $order = Order::find()->where(
                [
                    'sku_id' => $value['sku_id'],
                    'quantity' => $value['quantity'],
                    'shopper_id' => $shopperId,
                    'status' => Order::STATUS_CREATED,
                    'product_id' => $productId,
                ]
            )->exists();
            //create new if not
            if (empty($order)) {
                $order = new Order();
                $order->setAttributes(
                    [
                        'sku_id' => $value['sku_id'],
                        'quantity' => $value['quantity'],
                        'shopper_id' => $shopperId,
                        'status' => Order::STATUS_CREATED,
                        'product_id' => $productId,
                    ]
                );
                $order->save();
            }
            $parameters["sku{$value['sku_id']}"] = $value['quantity'];
            $skuIds[] = $value['sku_id'];
        }
        $parameters['shopperId'] = $shopperId;
        $parameters['pageName'] = "AUTO_LOGIN_PAGE";
        $parameters['expirationInMinutes'] = $this->module->expirationInMinutes;
        if (empty($parameters['thankyou.backtosellerurl']) && isset($this->module->backToSellerUrl)) {
            $parameters['thankyou.backtosellerurl'] = $this->module->backToSellerUrl;
        }
        //encrypt those who should be encrypted
        $toEncrypt = [];
        $haveToBeEncrypted = [
            "sku%dpriceamount", "sku%drecurringpriceamount", "sku%dpricecurrency",
            "sku%drecurringpricecurrency", "sku%dtrialdays", "thankyou.backtosellerurl",
            "shopperId", "expirationInMinutes", "pageName"
        ];
        foreach ($haveToBeEncrypted as $value) {
            foreach ($skuIds as $skuId) {
                $pastedVal = sprintf($value, $skuId);
                if (in_array($pastedVal, array_keys($parameters))) {
                    $toEncrypt[$pastedVal] = $parameters[$pastedVal];
                    unset($parameters[$pastedVal]);
                }
            }
        }
        if (!empty($toEncrypt)) {
            $enc = Yii::$app->bluesnap->encryptParams($toEncrypt);
        } else {
            $enc = '';
        }
        $result = $this->url."?enc=$enc&storeId=".$this->module->defaultStoreId;
        foreach ($parameters as $key => $value) {
            $result .= "&$key=$value";
        }
        return $result;
    }
}
