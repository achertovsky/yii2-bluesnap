<?php

namespace achertovsky\bluesnap\helpers;

use yii\httpclient\Client;
/**
 * Description of Request
 *
 * @author alexander
 */
class Request
{
    /**
     * @param string $url
     * @param string $data
     * @param array $headers
     * @return yii\httpclient\Request
     */
    public static function post($url, $data = '', $headers = [])
    {
        $client = new Client;
        return $client->post($url, $data, $headers)->send();
    }
    
    /**
     * @param string $url
     * @param string $data
     * @param array $headers
     * @return yii\httpclient\Request
     */
    public static function put($url, $data = '', $headers = [])
    {
        $client = new Client;
        return $client->put($url, $data, $headers)->send();
    }
    
    /**
     * @param string $url
     * @param array $headers
     * @return yii\httpclient\Request
     */
    public static function get($url, $headers = [])
    {
        $client = new Client;
        return $client->get($url, null, $headers)->send();
    }
}
