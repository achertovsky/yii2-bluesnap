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
            $inArray = in_array($fieldName, $ignore, true);
            if ($inArray || is_null($value)) {
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
        return self::formatXml($xmlArray);
    }
    
    /**
     * Gathers data from result of xml_parse_into_struct to better format
     * @param array $array
     * @return array
     */
    private static function formatXml($array)
    {
        $result = [];
        $routes = [];
        $multilinesTags = [];
        foreach ($array as $key => $data) {
            $tag = str_replace('-', '_', strtolower($data['tag']));
            $value = isset($data['value']) ? $data['value'] : '';
            if (in_array($value, ['true', 'false'])) {
                $value = $value == 'true' ? true : false;
            }
            switch ($data['type']) {
                case 'cdata':
                    $currentPointer = &$routes[$tag];
                    break;
                case 'open':
                    /**
                     * In case of open tag create new array key
                     * or find existing one and make it work point
                     */
                    if (!isset($routes[$tag])) {
                        $currentPointer = &$result;
                        foreach ($routes as $name => $route) {
                            $currentPointer = &$currentPointer[$name];
                            /**
                             * if tag came on path is multilevel - we guaranteed working with last element
                             * guarantee comes with fact that we going from top to bottom of xml
                             * filling always last element
                             */
                            if (in_array($name, $multilinesTags)) {
                                end($currentPointer);
                                $currentPointer = &$currentPointer[key($currentPointer)];
                            }
                        }
                        if (!isset($currentPointer[$tag]) && !in_array($tag, $multilinesTags)) {
                            //data is the only on level
                            $currentPointer[$tag] = [];
                            $routes[$tag] = &$currentPointer[$tag];
                            $currentPointer = &$routes[$tag];
                        } else {
                            //data level has multiple lines
                            if (!in_array($tag, $multilinesTags)) {
                                $multilinesTags[] = $tag;
                                $existingData = $currentPointer[$tag];
                                $currentPointer = [
                                    $tag => [
                                        'multilines' => true,
                                        $existingData,
                                    ],
                                ];
                            }
                            $currentPointer[$tag][] = [];
                            end($currentPointer[$tag]);
                            $lastKey = key($currentPointer[$tag]);
                            $routes[$tag] = &$currentPointer[$tag][$lastKey];
                            $currentPointer = &$currentPointer[$tag][$lastKey];
                        }
                    } else {
                        $currentPointer = &$routes[$tag];
                    }
                    break;
                case 'complete':
                    $currentPointer[$tag] = $value;
                    break;
                case 'close':
                    unset($routes[$tag]);
                    break;
            }
        }
        return $result;
    }
}
