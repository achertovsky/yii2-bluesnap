<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\Module;

/**
 * Contains common fields for all models of module
 * @author alexander
 */
class Core extends \yii\db\ActiveRecord
{
    /**
     * @var Module
     */
    public $module = null;
                
    /**
     * Returns array of non-empty vars
     * @return array
     */
    public function getData()
    {
        $result = [];
        $vars = get_object_vars($this);
        foreach ($vars as $name => $var) {
            if (empty($this->$name)) {
                continue;
            }
            $result[$name] = $var;
        }
        return $result; 
   }
}
