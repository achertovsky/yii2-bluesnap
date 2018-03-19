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

/**
 * Docs https://support.bluesnap.com/docs/ipn-setup
 *
 * @author alexander
 * @parameter array $sandboxIps
 * @parameter array $productionIps
 */
class IPN
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
     * Main enter point of IPN
     * Contains all supported types and correct response to bluesnap
     */
    public function handleIpn()
    {
        if (!in_array(Yii::$app->request->getUserIP(), $this->ips)) {
            Yii::info("Wrong ip");
            throw new \yii\web\NotFoundHttpException;
        }
        $post = Yii::$app->request->post();
        Yii::info("Got: ".var_export($post, true));
        if (isset($post['transactionType'])) {
            switch ($post['transactionType']) {
                case "CHARGE":
                    $this->handleCharge($post);
                    break;
                case "AUTH_ONLY":
                    $this->handleCharge($post);
                    break;
                case "CANCELLATION":
                    $this->handleCancel($post);
                    break;
                case "DECLINE":
                    $this->handleCancel($post);
                    break;
            }
        }
        
        $dataProtectionKey = Yii::$app->getModule(Yii::$app->bluesnap->moduleName)->dataProtectionKey;
        Yii::$app->response->setStatusCode(200);
        Yii::$app->response->content = md5("OK$dataProtectionKey");
    }
    
    /**
     * @param array $post
     * @return Order
     */
    public function findOrder($post)
    {
        $shopperId = $post['accountId'];
        $productId = $post['productId'];
        $skuId = $post['contractId'];
        $order = Order::find()->where(
            [
                'and',
                ['=', 'shopper_id', $shopperId],
                ['=', 'sku_id', $skuId],
                ['=', 'product_id', $productId],
            ]
        )->one();
        if (empty($order)) {
            Yii::error("No such order exist shopper_id: $shopperId; product_id: $productId");
        } else {
            $order->quantity = $post['quantity'];
        }
        return $order;
    }
    
    /**
     * @param array $post
     * @return bool
     */
    public function handleCharge($post)
    {
        $order = self::findOrder($post);
        if (empty($order)) {
            return;
        }
        $order->status = Order::STATUS_COMPLETED;
        return $order->save();
    }
    
    /**
     * @param array $post
     * @return bool
     */
    public function handleCancel($post)
    {
        $order = self::findOrder($post);
        if (empty($order)) {
            return;
        }
        $order->status = Order::STATUS_CANCELLED;
        return $order->save();
    }
}
