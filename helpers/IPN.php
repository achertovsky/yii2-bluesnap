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
        if (!in_array(Yii::$app->request->getUserIP(), $this->ips)) {
            Yii::info("Wrong ip");
            throw new \yii\web\NotFoundHttpException;
        }
        $this->post = Yii::$app->request->post();
        Yii::info("Got: ".var_export($this->post, true));
        if (isset($this->post['transactionType'])) {
            $this->status = null;
            switch ($this->post['transactionType']) {
                case "CHARGE":
                    $this->status = Order::STATUS_COMPLETED;
                    break;
                case "AUTH_ONLY":
                    $this->status = Order::STATUS_COMPLETED;
                    break;
                case "RECURRING":
                    $this->status = Order::STATUS_COMPLETED;
                    break;
                case "CANCELLATION":
                    $this->status = Order::STATUS_CANCELLED;
                    break;
                case "DECLINE":
                    $this->status = Order::STATUS_CANCELLED;
                    break;
                case "CANCELLATION_REFUND":
                    $this->status = Order::STATUS_CANCELLED;
                    break;
            }
            $this->handle();
            Event::trigger(IPN::className(), $this->post['transactionType'], new Event(['sender' => $this]));
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
     * @return bool
     */
    public function handle()
    {
        //ipns with no money is not supported by default
        if (!isset($this->post['invoiceAmountUSD'])) {
            return false;
        } 
        $order = Yii::$app->bluesnap->orderModel;
        $order->ipnPost = $this->post;
        $order->setAttributes(
            [
                'shopper_id' => $this->post['accountId'],
                'product_id' => $this->post['productId'],
                'sku_id' => $this->post['contractId'],
                'quantity' => $this->post['quantity'],
                'usd_amount' => $this->post['invoiceAmountUSD'],
            ]
        );
        if (isset($this->post['subscriptionId'])) {
            $order->subscription_id = $this->post['subscriptionId'];
        }
        $order->status = $this->status;
        if (!$order->validate()) {
            Yii::trace("Validation errors is: ".var_export($order->errors, true));
        }
        return $order->save();
    }
}
