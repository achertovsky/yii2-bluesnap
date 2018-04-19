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
    public static function prepareBody($wrapBy, $data, $firstLevel = true, $ignore = Xml::DEFAULT_IGNORE)
    {
        $wrapBy = str_replace('_', '-', $wrapBy);
        if ($firstLevel) {
            $result = "<$wrapBy xmlns='http://ws.plimus.com'>";
        } else {
            $result = "<$wrapBy>";
        }
        $result = empty($wrapBy) ? '' : $result;
        foreach ($data as $fieldName => $value) {
            $emptyVal = empty($value);
            $inArray = in_array($fieldName, $ignore, true);
            if ($inArray || $emptyVal) {
                continue;
            }
            $fieldName = str_replace('_', '-', $fieldName);
            if (is_numeric($fieldName)) {
                $fieldName = key($data[$fieldName]);
            }
            if (is_array($value)) {
                $result .= Xml::prepareBody(($fieldName == $wrapBy ? '' : $fieldName), $value, false, $ignore);
                continue;
            }
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }
            $result .= "<$fieldName>$value</$fieldName>";
        }
        $result .= empty($wrapBy) ? '' : "</$wrapBy>";
        return $result;
    }
    
    /**
     * Gonna return parsed XML
     * @param string $xml
     * @return array
     */
    public static function parse($xml)
    {
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, $xml, $xmlArray);
        xml_parser_free($parser);
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
                unset($array[$key]);
                return $result;
            }
            $tag = str_replace('-', '_', strtolower($xmlPart['tag']));
            $value = isset($xmlPart['value']) ? $xmlPart['value'] : '';
            switch ($type) {
                case 'open':
                    if ($openName == '') {
                        //if no open tag - gather it
                        $openName = $tag;
                    } else {
                        //if there is open tag - it means that this item is sublevel, gather data from lower level
                        $result[$openName] = ArrayHelper::merge(
                            isset($result[$openName]) ? $result[$openName] : [],
                            self::getLevelData($array)
                        );
                    }
                    break;
                case 'complete':
                    //if tag is completed - store info
                    if (in_array($value, ['true', 'false'])) {
                        $value = $value == 'true' ? true : false;
                    }
                    $result[$openName][$tag] = $value;
                    break;
            }
            //drop from list those who was used
            unset($array[$key]);
        }
        return $result;
    }
}
