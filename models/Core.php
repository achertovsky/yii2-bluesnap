<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\Module;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

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
    
    /** @inheritdoc */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    'class' => TimestampBehavior::className(),
                ],
            ]
        );
    }
    
    /**
     * Fields set in this array gonna be encoded\decoded to json in right places to make it writable to db and work in validation
     * @var array
     */
    public $jsonFields = [];
    
    /**
     * encoding/decoding fields that is arrays/json
     * @param type $action
     */
    public function processArrays($action = 'encode')
    {
        $arrayFields = $this->jsonFields;
        foreach ($arrayFields as $field) {
            if ($action == 'encode') {
                if (is_array($this->$field)) {
                    $this->$field = Json::$action($this->$field);
                }
            } elseif ($action == 'decode') {
                try {
                    $this->$field = Json::$action($this->$field);
                } catch (\Exception $ex) {
                    $this->$field = null;
                }
            }
        }
    }
    
    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->processArrays();
            return true;
        }
        return false;
    }
    
    /** @inheritdoc */
    public function afterValidate()
    {
        $this->processArrays('decode');
        parent::afterValidate();
    }
    
    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->processArrays();
            return true;
        }
        return false;
    }
    
    /** @inheritdoc */
    public function afterFind()
    {
        parent::afterFind();
        $this->processArrays('decode');
    }
}
