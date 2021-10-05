<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\helpers\Xml;
use achertovsky\bluesnap\traits\Common;
use achertovsky\bluesnap\helpers\Request;

/**
 * @author Alexander Chertovsky
 */
class Report extends \yii\base\Object
{
    use Common;
    
    /**
     * List of urls for api requests
     * @var string
     */
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/report/';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/report/';

    /**
     * Returns response for subscription
     * @param int $subscriptionId
     * @return array
     */
    public function getReport($reportCode, $parameters)
    {
        $content = Request::get(
            $this->url.$reportCode,
            $parameters,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        )->getContent();
        return Xml::parse($content);
    }
}
