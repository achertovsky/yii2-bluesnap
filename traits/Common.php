<?php

namespace achertovsky\bluesnap\traits;

use achertovsky\bluesnap\Module;

trait Common
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
   
   /**
    * @var string 
    */
   protected $url;
   
    /**
     * Sets url that gonna be used to requests
     */
    public function setUrl()
    {
        if ($this->module->sandbox) {
            $this->url = $this->sandboxUrl;
        } else {
            $this->url = $this->liveUrl;
        }
    }
}
