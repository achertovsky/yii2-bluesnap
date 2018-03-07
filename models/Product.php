<?php

namespace achertovsky\bluesnap\models;

use yii\helpers\ArrayHelper;
use achertovsky\bluesnap\helpers\Xml;
use achertovsky\bluesnap\helpers\Request;
use achertovsky\bluesnap\models\Sku;

/**
 * @author alexander
 * 
 * Contains all logic related to bluesnap extended api related to product
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $product_id
 * @property string $product_status
 * @property string $product_name
 * @property string $product_short_description
 * @property string $product_long_description
 * @property string $product_info_url
 * @property string $product_image
 * @property string $product_merchant_descriptor
 * @property string $product_support_email
 */
class Product extends Core
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'bluesnap_product';
    }
    /**
     * List of possible product status codes
     */
    const PRODUCT_STATUS_ACTIVE = 'A';
    const PRODUCT_STATUS_INACTIVE = 'I';
    const PRODUCT_STATUS_DELETED = 'D';
    
    /**
     * List of urls for api requests
     * @var string
     */
    protected $url = '';
    protected $sandboxUrl = 'https://sandbox.bluesnap.com/services/2/catalog/products';
    protected $liveUrl = 'https://ws.bluesnap.com/services/2/catalog/products';
    
    /** @inheritdoc */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'create' => [
                    'product_short_description', 'product_name'
                ]
            ]
        );
    }
    
    /** @inheritdoc */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['product_id'], 'unique'],
                [['created_at', 'updated_at', 'product_id'], 'integer'],
                [['product_id', 'product_name', 'product_short_description'], 'required'],
                [['product_image'], 'url'],
                [['product_support_email'], 'email'],
                [
                    [
                        'product_name', 'product_short_description', 'product_info_url', 'product_image',
                        'product_long_description', 'product_merchant_descriptor', 'product_support_email',
                    ],
                    'string'
                ],
                [['product_status'], 'string', 'max' => 1, 'min' => 1,],
                ['product_skus', 'safe'],
            ]
        );
    }
    
    /**
     * @param string $productName
     * @param string $productDesc
     */
    public function defineMinimalRequirements($productName, $productDesc)
    {
        $this->product_name = $productName;
        $this->product_short_description = $productDesc;
    }
    
    /**
     * Creates product on bluesnap and saves it to database
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/create-product
     * @param string $presetStatus
     * @return boolean|\achertovsky\bluesnap\models\Product
     */
    public function createProduct($presetStatus = Product::PRODUCT_STATUS_ACTIVE)
    {
        $this->product_status = $presetStatus;
        //prevalidate
        $this->setScenario('create');
        if (!$this->validate()) {
            return false;
        }
        $body = Xml::prepareBody('product', $this->getAttributes());
        $this->setScenario('default');
        $response = Request::post(
            $this->url,
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        //201 means that product is created
        if ($code == 201) {
            //get productId from response
            $headers = $response->getHeaders();
            if (isset($headers['location'])) {
                $location = $headers['location'];
                $this->product_id = substr($location, strrpos($location, '/')+1);
            }
            if ($this->save()) {
                return $this;
            }
        }
        
        return false;
    }
    
    /**
     * Sends update and saves it to database
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/update-product
     * @return boolean|\achertovsky\bluesnap\models\Product
     */
    public function updateProduct()
    {
        if (!$this->validate()) {
            return false;
        }
        $body = Xml::prepareBody('product', $this->getAttributes());
        $response = Request::put(
            $this->url.'/'.$this->product_id,
            $body,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        );
        $code = $response->getStatusCode();
        //docs says 204 - success
        if ($code == 204) {
           if ($this->save()) {
            return $this;
        } 
        }
        return false;
    }
    
    /**
     * Sends delete and saves it to database
     * Docs: https://developers.bluesnap.com/v8976-Extended/docs/update-product
     * @return boolean|\achertovsky\bluesnap\models\Product
     */
    public function deleteProduct()
    {
        $this->product_status = Product::PRODUCT_STATUS_DELETED;
        return $this->updateProduct();
    }
    
    /**
     * Receives data from api, able to save
     * @param string $productId
     * @param bool $saveToDb
     * @return boolean|\achertovsky\bluesnap\models\Product
     */
    public function getProduct($productId, $saveToDb = false)
    {
        $this->product_id = $productId;
        $content = Request::get(
            $this->url.'/'.$productId,
            [
                'Content-Type' => 'application/xml',
                'Authorization' => $this->module->authToken,
            ]
        )->getContent();
        $response = Xml::parse($content);
        $this->setAttributes($response['product']);
        if ($saveToDb) {
            if (!$this->validate() || !$this->save()) {
                return false;
            }
        }
        return $this;
    }
    
    /**
     * @var array
     */
    public $product_skus;
    
    /** @inheritdoc */
    public function attributes() {
        return ArrayHelper::merge(
            parent::attributes(),
            [
                'product_sku',
            ]
        );
    }
    
    /**
     * Getting sku if its insert
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $product = $this->getProduct($this->product_id);
            $sku = new Sku(
                [
                    'module' => $this->module,
                ]
            );
            $sku->setUrl();
            $location = $product->product_skus['url'];
            $sku->sku_id = substr($location, strrpos($location, '/')+1);
            $sku->getSku();
        }
    }
}
