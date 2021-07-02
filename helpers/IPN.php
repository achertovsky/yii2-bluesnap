<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace achertovsky\bluesnap\helpers;

use achertovsky\bluesnap\traits\Common;
use achertovsky\bluesnap\models\Order;
use Yii;
use yii\base\Event;

/**
 * Docs https://support.bluesnap.com/docs/ipn-setup
 *
 * @author alexander
 * @parameter array $sandboxIps
 * @parameter array $productionIps
 */
class IPN extends \yii\base\Object
{
    use Common;
    /**
     * @var array 
     */
    protected $ips = [];
    
    /**
     * List of ips where ipn can come from
     * @var array
     */
    protected $sandboxIps = [
        '209.128.93.232',
        '62.216.234.196',
        '38.99.111.50',
        '38.99.111.150',
        '141.226.140.200',
        '141.226.141.200',
        '141.226.142.200',
        '141.226.143.200',
        //to be able send posts from local. why not?
        '127.0.0.1',
    ];
    
    /**
     * List of ips where ipn can come from
     * @var array
     */
    protected $productionIps = [
        '62.216.234.216',
        '209.128.93.254',
        '209.128.93.98',
        '38.99.111.60',
        '38.99.111.160',
        '141.226.140.100',
        '141.226.141.100',
        '141.226.142.100',
        '141.226.143.100',
    ];
    
    /**
     * setting ips for future check
     */
    public function setIps()
    {
        if ($this->module->sandbox) {
            $this->ips = $this->sandboxIps;
        } else {
            $this->ips = $this->productionIps;
        }
    }
    
    /**
     * List of available events (by default contain list of only default ipns)
     * Docs: https://support.bluesnap.com/docs/default-ipns
     */
    const EVENT_AUTH_ONLY = "AUTH_ONLY";
    const EVENT_CANCELLATION = "CANCELLATION";
    const EVENT_CANCELLATION_REFUND = "CANCELLATION_REFUND";
    const EVENT_CANCEL_ON_RENEWAL = "CANCEL_ON_RENEWAL";
    const EVENT_CHARGE = "CHARGE";
    const EVENT_CHARGEBACK = "CHARGEBACK";
    const EVENT_CHARGEBACK_STATUS_CHANGED = "CHARGEBACK_STATUS_CHANGED";
    const EVENT_CONTRACT_CHANGE = "CONTRACT_CHANGE";
    const EVENT_DECLINE = "DECLINE";
    const EVENT_RECURRING = "RECURRING";
    const EVENT_REFUND = "REFUND";
    const EVENT_SUBSCRIPTION_REMINDER = "SUBSCRIPTION_REMINDER";
    
    /**
     * Contains all post request of IPN
     * @var array
     */
    public $post = [];
    
    /**
     * Main enter point of IPN
     * Contains all supported types and correct response to bluesnap
     */
    public function handleIpn()
    {
        if (!in_array(Yii::$app->request->getUserIP(), $this->ips) && !$this->module->sandbox) {
            Yii::info("Wrong ip");
            throw new \yii\web\NotFoundHttpException;
        }
        $this->post = Yii::$app->request->post();
        Yii::info("Got: ".var_export($this->post, true));
        if (isset($this->post['transactionType'])) {
            $this->status = null;
            switch ($this->post['transactionType']) {
                case "CHARGE":
                case "AUTH_ONLY":
                case "RECURRING":
                case "REFUND":
                case "CHARGEBACK":
                    $this->status = Order::STATUS_COMPLETED;
                    break;
                case "CANCELLATION":
                case "DECLINE":
                case "CANCELLATION_REFUND":
                case "CANCEL_ON_RENEWAL":
                    $this->status = Order::STATUS_CANCELLED;
                    break;
            }
            $this->handle();
            Event::trigger(IPN::class, $this->post['transactionType'], new Event(['sender' => $this]));
        }
        
        $dataProtectionKey = Yii::$app->getModule(Yii::$app->bluesnap->moduleName)->dataProtectionKey;
        Yii::$app->response->setStatusCode(200);
        Yii::$app->response->content = md5("OK$dataProtectionKey");
    }
    
    /**
     * If this value set - will use findOrder with this status for order
     * @var int
     */
    protected $status = null;
    
    /**
     * @return void
     */
    public function handle()
    {
        //ipns with no money is not supported by default
        if (!isset($this->post['invoiceAmountUSD'])) {
            return false;
        }
        $order = self::getOrderModel(
            $this->post['accountId'],
            $this->post['productId'],
            $this->post['contractId'],
            $this->post['referenceNumber'],
            $this->post['quantity'],
            $this->post['invoiceAmountUSD'],
            $this->status,
            $this->post
        );
        if (isset($this->post['subscriptionId'])) {
            Yii::debug("Main product is subscription");
            $order->subscription_id = $this->post['subscriptionId'];
        }
        $result = false;
        if ($order->validate()) {
            // update all previous subscriptions to cancelled status before save new one
            if (!empty($this->post['subscriptionId'])) {
                Yii::debug("Item is subscription, cancel previous subscription orders");
                Order::updateAll(
                    [
                        'status' => Order::STATUS_CANCELLED,
                    ],
                    [
                        'subscription_id' => $this->post['subscriptionId'],
                    ]
                );
            }
            $order->save();
        } else {
            Yii::error("Validation errors is: ".var_export($order->errors, true));
        }
        
        //upsales
        if (isset($this->post['promoteContractsNum']) && $this->post['promoteContractsNum'] > 0) {
            Yii::debug($this->post['promoteContractsNum']." promote contracts was found");
            for ($i = 0; $i < $this->post['promoteContractsNum']; $i++) {
                $order = self::getOrderModel(
                    $this->post['accountId'],
                    $this->post["promoteProductId$i"],
                    $this->post["promoteContractId$i"],
                    $this->post['referenceNumber'],
                    $this->post["promoteContractQuantity$i"],
                    $this->post["promoteContractPrice$i"],
                    $this->status,
                    $this->post
                );
                if (!$order->save()) {
                    Yii::error("Validation errors is: ".var_export($order->errors, true));
                }
            }
        }
    }

    /**
     * Code sugar
     *
     * @param integer $shopperId
     * @param integer $productId
     * @param integer $skuId
     * @param integer $referenceNumber
     * @param integer $quantity
     * @param double $usdAmount
     * @param integer $status
     * @param array $postData
     * @return \achertovsky\bluesnap\models\Order
     */
    protected static function getOrderModel($shopperId, $productId, $skuId, $referenceNumber, $quantity, $usdAmount, $status, $postData)
    {
        $order = Yii::$app->bluesnap->getOrderModel(
            [
                'shopper_id' => $shopperId,
                'product_id' => $productId,
                'sku_id' => $skuId,
                'reference_number' => $referenceNumber,
            ],
            'id',
            true
        );
        if (empty($order)) {
            $order = Yii::$app->bluesnap->orderModel;
        }
        $order->ipnPost = $postData;
        $order->setAttributes(
            [
                'shopper_id' => $shopperId,
                'product_id' => $productId,
                'sku_id' => $skuId,
                'reference_number' => $referenceNumber,
                'quantity' => $quantity,
                'usd_amount' => $usdAmount,
                'status' => $status,
            ]
        );
        return $order;
    }
}
