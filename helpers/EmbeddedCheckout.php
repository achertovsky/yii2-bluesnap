<?php

namespace achertovsky\bluesnap\helpers;

use Yii;
use yii\base\BaseObject;
use yii\httpclient\Response;

/**
 * Embedded checkout-related functionality
 */
class EmbeddedCheckout extends BaseObject
{
    /**
     * Base host for embedded checkout
     */
    const HOST_PRODUCTION = 'https://ws.bluesnap.com';
    const HOST_SANDBOX = 'https://sandbox.bluesnap.com';

    /**
     * Route to get embedded checkout token
     */
    const ROUTE = '/services/2/payment-fields-tokens';

    /**
     * Depends on environment set in config returns base domain
     *
     * @return string
     */
    public static function getHost($moduleName = 'bluesnap')
    {
        $module = Yii::$app->getModule($moduleName);
        if ($module->sandbox) {
            return self::HOST_SANDBOX;
        }
        return self::HOST_PRODUCTION;
    }

    /**
     * Does request to bluesnap and gets embedded checkout token
     *
     * @param string $moduleName
     * @return string
     */
    public static function getToken($moduleName = 'bluesnap')
    {
        $module = Yii::$app->getModule($moduleName);
        $host = self::getHost($moduleName);
        $url = $host.self::ROUTE;
        /** @var Response $response */
        $response = Request::post(
            $url,
            '',
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $module->authToken,
            ]
        );
        $headers = $response->getHeaders();
        $location = $headers->get('Location');
        if (empty($location)) {
            return '';
        }
        $token = str_replace($url.'/', '', $location);
        return $token;
    }
}
