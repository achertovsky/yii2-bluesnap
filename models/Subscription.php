<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\helpers\Xml;
use achertovsky\bluesnap\helpers\Request;
use achertovsky\bluesnap\traits\Common;
use Yii;

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
            null,
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
        return $this->updateSubscription(
            [
                'subscription_id' => $subscriptionId,
                'status' => Subscription::STATUS_ACTIVE,
                'shopper_id' => $shopperId,
                'underlying_sku_id' => $contractId,
            ]
        );
    }
    
    /**
     * @param int $subscriptionId
     * @return boolean
     */
    public function cancelSubscription($subscriptionId)
    {
        return $this->updateSubscription(
            [
                'subscription_id' => $subscriptionId,
                'status' => Subscription::STATUS_CANCELLED,
            ]
        );
    }
    
    /**
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/update-subscription
     * @param array $params
     * Required to have $params['subscription_id']
     * @return boolean
     */
    public function updateSubscription($params)
    {
        $body = Xml::prepareBody(
            'subscription',
            $params
        );
        $response = Request::put(
            $this->url.$params['subscription_id'],
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
        $this->errors = $error = Xml::parse($response->getContent());
        Yii::error(var_export($error, true));
        return false;
    }
}
