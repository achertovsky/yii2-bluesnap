<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\traits\Common;
use achertovsky\bluesnap\helpers\Xml;
use achertovsky\bluesnap\helpers\Request;
use Exception;
use Yii;

class Encrypt
{
    use Common;
    /**
     * List of urls for api requests
     * @var string
     */
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/tools/param-encryption';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/tools/param-encryption';
    
    /**
     * Gonna encrypt params set
     * @param array $params
     * @return string
     */
    public function encryptParams($params)
    {
        $parameters = [];
        foreach ($params as $key => $value) {
            if ($key == 'thankyou.backtosellerurl') {
                $value = urlencode($value);
            }
            $parameters['parameters'][] = [
                'parameter' => [
                    'param-key' => $key,
                    'param-value' => $value,
                ],
            ];
        }
        $body = Xml::prepareBody('param-encryption', $parameters);
        $response = Request::post(
            $this->url,
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        $content = $response->getContent();
        $data = Xml::parse($content);
        if ($code == 200) {
            if (isset($data['param_encryption']['encrypted_token'])) {
                return $data['param_encryption']['encrypted_token'];
            }
        } else {
            if (isset($data['messages']['message'])) {
                Yii::error($data['messages']['message']['error_name']." ".$data['messages']['message']['description']);
            }
        }
        throw new Exception(var_export($content, true));
        return null;
    }
    
    /**
     * Gonna decrypt params set
     * @param string $encryptedToken
     * @return string
     */
    public function decryptParams($encryptedToken)
    {
        $body = Xml::prepareBody('param-decryption', ['encrypted-token' => $encryptedToken]);
        $response = Request::post(
            str_replace('encryption', 'decryption', $this->url),
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        $content = $response->getContent();
        $data = Xml::parse($content);
        if ($code == 200) {
            if (isset($data['param_decryption']['decrypted_token'])) {
                $result = [];
                parse_str($data['param_decryption']['decrypted_token'], $result);
                return $result;
            }
        }
        throw new Exception(var_export($content, true));
        return null;
    }
}
