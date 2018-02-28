<?php

namespace achertovsky\bluesnap\helpers;

/**
 * @author alexander
 */
class Xml extends \yii\base\Model
{
    /**
     * contains fields that gonna be ignored by request anyway
     */
    const DEFAULT_IGNORE = ['id', 'created_at', 'updated_at'];
    /**
     * @param string $wrapBy
     * @param array $data
     */
    public static function prepareBody($wrapBy, $data, $ignore = Xml::DEFAULT_IGNORE)
    {
        $result = "<$wrapBy xmlns='http://ws.plimus.com'>";
        foreach ($data as $fieldName => $value) {
            if (in_array($fieldName, $ignore) || empty($value)) {
                continue;
            }
            $fieldName = str_replace('_', '-', $fieldName);
            $result .= "<$fieldName>$value</$fieldName>";
        }
        $result .= "</$wrapBy>";
        return $result;
    }
}
