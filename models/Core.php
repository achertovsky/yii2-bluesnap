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
        foreach (get_object_vars($this) as $var) {
            if (empty($this->$var)) {
                continue;
            }
            $result[$var] = $this->$var;
        }
        return $result; 
   }
}
