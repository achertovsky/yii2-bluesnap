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
    public static function post($url, $data = '', $headers = [])
    {
        $client = new Client;
        return $client->post($url, $data, $headers)->send();
         
    }
}
