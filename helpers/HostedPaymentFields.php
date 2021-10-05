<?php

namespace achertovsky\bluesnap\helpers;

use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;
use achertovsky\bluesnap\traits\Common;

/**
 * https://developers.bluesnap.com/v8976-Tools/docs/hosted-payment-fields
 */
class HostedPaymentFields extends BaseObject
{
    use Common;

    /**
     * Endpoints
     */
    public $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/payment-fields-tokens';
    public $liveUrl = 'https://ws.bluesnap.com/services/2/payment-fields-tokens';

    /**
     * Gets domain from endpoint
     *
     * @return string domain with trailing slash
     */
    public function getDomain()
    {
        preg_match('/https:\/\/[a-zA-Z\.]+\//', $this->url, $result);
        return $result[0];
    }

    /**
     * Receives token for hosted fields
     *
     * @param string $moduleName
     * @return void
     */
    public function getToken($moduleName = 'bluesnap')
    {
        $module = Yii::$app->getModule($moduleName);
        $client = new Client();
        $response = $client->post(
            $this->url,
            '',
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $module->authToken,
            ]
        )->send();
        $headers = $response->getHeaders();
        if ($headers->has('location')) {
            $location = $headers->get('location');
            $token = substr($location, strrpos($location, '/')+1);
            return $token;
        }
        return null;
    }
}