<?php

namespace achertovsky\bluesnap\helpers;

use yii\helpers\ArrayHelper;

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
    
    /**
     * Gonna return parsed XML
     * @param string $xml
     * @return array
     */
    public static function parse($xml)
    {
        $xmlArray = [];
        $xmlData = xml_parse_into_struct(xml_parser_create(), $xml, $xmlArray);
        $result = self::getLevelData($xmlArray);
        return $result;
    }
    
    /**
     * RECURSIVE
     * Gathers data from result of xml_parse_into_struct to better format
     * @param array $array
     * @return array
     */
    private static function getLevelData(&$array)
    {
        //predefine
        $openName = '';
        $result = [];
        //while used instead of foreach cause foreach works with copy of income array
        while (!empty($array)) {
            //emulate key => value in while
            $xmlPart = reset($array);
            $key = key($array);
            
            $type = $xmlPart['type'];
            if ($type == 'close') {
                //if close tag found - ignore it
                return $result;
            }
            $tag = str_replace('-', '_', strtolower($xmlPart['tag']));
            $value = $xmlPart['value'];
            switch ($type) {
                case 'open':
                    if ($openName == '') {
                        //if no open tag - gather it
                        $openName = $tag;
                    } else {
                        //if there is open tag - it means that this item is sublevel, gather data from lower level
                        $result[$openName] = ArrayHelper::merge($result[$openName], self::getLevelData($array));
                    }
                    break;
                case 'complete':
                    //if tag is completed - store info
                    $result[$openName][$tag] = $value;
                    break;
            }
            //drop from list those who was used
            unset($array[$key]);
        }
    }
}
