<?php

namespace achertovsky\bluesnap\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class BillingInfoForm extends Model
{
    /**
     * @var string
     */
    public $firstname;
    /**
     * @var string
     */
    public $lastname;
    /**
     * @var string
     */
    public $zip;
    /**
     * @var string
     */
    public $country;
    /**
     * @var string
     */
    public $state;
    /**
     * @var string
     */
    public $address1;
    /**
     * @var string
     */
    public $address2;
    /**
     * @var string
     */
    public $city;
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'zip', 'country', 'state', 'address1', 'address2', 'city'], 'required'],
            [['firstname', 'lastname', 'street', 'state', 'city', 'country', 'zip'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'address1' => 'Address',
                'address2' => 'Second Address',
            ]
        );
    }

    /**
     * Will process model fields into names described in
     * https://developers.bluesnap.com/v8976-Extended/docs/billing-contact-info
     *
     * @return array
     */
    public function toBluesnapArray()
    {
        $array = self::toArray();
        $array['first-name'] = $array['firstname'];
        $array['last-name'] = $array['lastname'];
        unset($array['firstname'], $array['lastname']);
        return $array;
    }
}
