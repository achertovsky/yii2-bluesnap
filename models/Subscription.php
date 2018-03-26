<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\helpers\Xml;
use achertovsky\bluesnap\helpers\Request;
use achertovsky\bluesnap\traits\Common;

/**
 * @author Alexander Chertovsky
 */
class Subscription extends \yii\base\Object
{
    use Common;
    
    /**
     * List of urls for api requests
     * @var string
     */
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/subscriptions/';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/subscriptions/';
    
    /**
     * Returns response for subscription
     * @param int $subscriptionId
     * @return array
     */
    public function getSubscription($subscriptionId)
    {
        $content = Request::get(
            $this->url.$subscriptionId,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        )->getContent();
        return Xml::parse($content);
    }
    
    /**
     * @var mixed 
     */
    public $errors = null;
    
    /**
     * List of possible statuses
     */
    const STATUS_ACTIVE = "A";
    const STATUS_CANCELLED = "C";
    const STATUS_DECLINED = "D";
    
    /**
     * Changes sku for subscription
     * @param int $subscriptionId
     * @param int $contractId
     * @param int $shopperId
     * @return boolean
     */
    public function switchSubscriptionContract($subscriptionId, $contractId, $shopperId)
    {
        $body = Xml::prepareBody(
            'subscription',
            [
                'subscription_id' => $subscriptionId,
                'status' => Subscription::STATUS_ACTIVE,
                'shopper_id' => $shopperId,
                'underlying_sku_id' => $contractId,
            ]
        );
        $response = Request::put(
            $this->url.$subscriptionId,
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        if ($code == 204) {
            return true;
        }
        $this->errors = Xml::parse($response->getContent());
        return false;
    }
}
