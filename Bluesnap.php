<?php

namespace achertovsky\bluesnap;

use yii\base\InvalidConfigException;
use achertovsky\bluesnap\models\Product;
use Yii;

/**
 * Component contains all required actions
 *
 * @author alexander
 */
class Bluesnap extends \yii\base\Object
{
    /**
     * Name of module. It should be identical to name in config `modules` section
     * @var string 
     */
    public $moduleName = 'bluesnap';
    
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        if (empty(Yii::$app->getModule($this->moduleName))) {
            throw new InvalidConfigException("No {$this->moduleName} found in project modules config");
        }
    }
    
    /**
     * Creates new or returns list by filter
     * @param array $where
     * @return Product
     */
    public function getProductModel($where = [])
    {
        if (empty($where)) {
            $model = new Product();
            $model->module = Yii::$app->getModule($this->moduleName);
            $model->setUrl();
            return $model;
        }
        $models = Product::find()->where($where)->all();
        foreach ($models as $key => $model) {
            $model->module = Yii::$app->getModule($this->moduleName);
            $model->setUrl();
            $models[$key] = $model;
        }
        return $models;
    }
}
