<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\traits\Common;
/**
 * Wrapper for https://developers.bluesnap.com/v8976-Extended/docs/shopper-info
 * @author alexander
 * @property string $seller_shopper_id
 * @property string $store_id
 * @property string $vat_code
 * @property string $shopper_currency
 * @property string $locale
 * @property string $soft_descriptor
 * @property string $descriptor_phone_number
 * @property array $invoice_contacts_info
 * @property array $shipping_contact_info
 * @property array $shopper_contact_info
 * @property array $payment_info
 */
class ShopperInfo extends \yii\base\Model
{
    use Common;
    /**
     * Unique shopper ID assigned by the merchant.
     * @var string
     */
    public $seller_shopper_id;
    
    /**
     * Merchant’s store ID.
     * To pick up the soft descriptor configured in the system you will need to include the store-id. Please contact merchants@bluesnap.com if you do not know your store-id
     * @var int 
     */
    public $store_id;
    
    /**
     * Value added tax identification number (VATIN) associated with the shopper. A shopper with a VATIN does not pay VAT to the merchant.
     * @var string 
     */
    public $vat_code;
    
    /**
     * Shopper's selected currency. All prices and orders for this shopper will be based on this currency.
     * See https://developers.bluesnap.com/docs/currency-codes
     * Note: In the Create Order and New Shopper request, the shopper currency and order currency must match.
     * @var string
     */
    public $shopper_currency = "USD";
    
    /**
     * Shopper's selected language. See https://developers.bluesnap.com/docs/language-codes
     * @var string 
     */
    public $locale = 'en';
    
    /**
     * NOTE: Relevant if sending a credit card number
     * Description that may appear on the shopper's bank statement when BlueSnap validates the card.
     * @var string 
     */
    public $soft_descriptor;
    
    /**
     * NOTE: Relevant if sending a credit card number
     * Merchant's support phone number, which may appear on the shopper's bank statement when BlueSnap validates the card.
     * Length: 0-20 characters
     * @var string 
     */
    public $descriptor_phone_number;
    
    /**
     * @var array 
     */
    public $invoice_contacts_info;
    
    /**
     * @param array $data
     * To fill refer to https://developers.bluesnap.com/v8976-Extended/docs/invoice-contact-info
     */
    public function setInvoiceContactsInfo($data)
    {
        $this->invoice_contacts_info = [
            'invoice_contact_info' => $data
        ];
    }
    
    /**
     * @var array
     */
    public $shipping_contact_info;
    
    /**
     * <b>Should be used after setShopperContactInfo() or when this data already present</b>
     */
    public function setShippingContactInfo()
    {
        $info = $this->gatherBillingInfo();
        foreach ($info as $key => $value) {
            $this->shipping_contact_info[$key] = $value;
        }
    }
    
    /**
     * @var array
     */
    public $shopper_contact_info;
    
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $address1
     * Address line 1 for shipping.
     * Between 2-42 characters.
     * @param string $city
     * @param string $zip
     * @param string $country
     * Country code for shipping.
     * See https://developers.bluesnap.com/docs/country-codes
     * @param string $phone
     * @param string $state
     * State code for shipping.
     * See https://developers.bluesnap.com/docs/state-and-province-codes.
     * Supports US and Canada states only. For states in other countries, it is necessary to include the state in the address property.
     * @param string $personalIdentificationNumber
     * <b>NOTE:</b> Required for local LatAm processing in Brazil. See https://developers.bluesnap.com/docs/latam-local-processing
     * The shopper's local personal identification number. These are the ID types per country:
     * Argentina - DNI (length 7-11 chars)
     * Brazil - CPF/CNPJ (length 11-14 chras)
     * Chile - RUN (length 8-9 chars)
     * Colombia - CC (length 6-10 chars)
     * Mexico - CURP/RFC (length 10-18 chars)
     * @param string $address2
     * Same as address1
     * @param string $companyName
     * @param string $fax
     */
    public function setShopperContactInfo(
        $firstName,
        $lastName,
        $email,
        $address1,
        $city,
        $zip,
        $country,
        $phone,
        $state = null,
        $personalIdentificationNumber = null,
        $address2 = null,
        $companyName = null,
        $fax = null
    ) {
        $this->shopper_contact_info = [
            'title' => $firstName.' '.$lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'address1' => $address1,
            'city' => $city,
            'zip' => $zip,
            'country' => $country,
            'phone' => $phone,
        ];
        
        if (!is_null($companyName)) {
            $this->shopper_contact_info['company_name'] = $companyName;
        } 
        if (!is_null($address2)) {
            $this->shopper_contact_info['address2'] = $address2;
        } 
        if (!is_null($state)) {
            $this->shopper_contact_info['state'] = $state;
        } 
        if (!is_null($personalIdentificationNumber)) {
            $this->shopper_contact_info['personal_identification_number'] = $personalIdentificationNumber;
        } 
        if (!is_null($fax)) {
            $this->shopper_contact_info['fax'] = $fax;
        } 
    }
    
    /**
     * For ecp/sepa use https://developers.bluesnap.com/v8976-Extended/docs/payment-info
     * Supports only credit cards
     * @var array
     */
    public $payment_info;
    
    /**
     * <b>Should be used after setShopperContactInfo() or when this data already present</b>
     * @param string $cardNumber
     * Credit card number.
     * For example:
     * 4111111111111111
     * 4111-1111-1111-1111
     * 4111 1111 1111 1111
     * @param string $expirationMonth
     * @param string $expirationYear
     * @param string $securityCode
     * @param string $cardLastFourDigits
     * Required if sending shopper-id and the shopper has multiple saved cards.
     * @param string $cardType
     * Required if sending shopper-id and the shopper has multiple saved cards.
     */
    public function addCreditCard($cardNumber, $expirationMonth, $expirationYear, $securityCode, $cardLastFourDigits = null, $cardType = null)
    {
        $cardInfo = [
            'card_number' => $cardNumber,
            'expiration_month' => $expirationMonth,
            'expiration_year' => $expirationYear,
            'security_code' => $securityCode,
        ];
        if (!is_null($cardLastFourDigits)) {
            $cardInfo['card_last_four_digits'] = $cardLastFourDigits;
        }
        if (!is_null($cardType)) {
            $cardInfo['card_type'] = $cardType;
        }
        $this->payment_info['credit_cards_info'][] = [
            'credit_card_info' => [
                'billing_contact_info' => $this->gatherBillingInfo(),
                'credit_card' => $cardInfo,
            ]
        ];
    }
    
    /**
     * <b>Should be used after setShopperContactInfo() or when this data already present</b>
     * Gonnna gather billing info from shopper contact info
     * @throws \yii\base\InvalidConfigException
     * @return array
     */
    public function gatherBillingInfo()
    {
        if (empty($this->shopper_contact_info)) {
            throw new \yii\base\InvalidConfigException("No shopper contact info");
        }
        $ignoreForBilling = ['company_name', 'fax', 'phone', 'email', 'title'];
        $billingInfo = [];
        foreach ($this->shopper_contact_info as $key => $value) {
            if (in_array($key, $ignoreForBilling)) {
                continue;
            }
            $billingInfo[$key] = $value;
        }
        return $billingInfo;
    }
    
    /**
     * <b>Should be used after setShopperContactInfo() or when this data already present</b>
     * @param type $cardLastFourDigits
     * @param type $cardType
     */
    public function deleteCreditCard($cardLastFourDigits, $cardType)
    {
        $this->payment_info['credit_cards_info'][] = [
            'credit_card_info' => [
                'billing_contact_info' => $this->gatherBillingInfo(),
                'credit_card' => [
                    'card_last_four_digits' => $cardLastFourDigits,
                    'card_type' => $cardType,
                ],
                'status' => 'D',
            ]
        ];
    }
}
