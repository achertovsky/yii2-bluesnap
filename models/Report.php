<?php

namespace achertovsky\bluesnap\models;

use yii\helpers\Json;
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
     * Returns reports
     * https://developers.bluesnap.com/v8976-Tools/docs/get-report-data
     *
     * @param string $reportCode
     * @param mixed $parameters
     * @return array
     */
    public function getReport($reportCode, $parameters = null)
    {
        $content = Request::get(
            $this->url.$reportCode,
            $parameters,
            [
                'Authorization' => $this->module->authToken,
            ]
        )->getContent();
        return Json::decode($content);
    }
}
