<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\traits\Common;
use yii\base\Model;

/**
 * @author alexander
 */
class ChargePolicy extends Model
{
    use Common;
    /** @var array */
    public $one_time_charge;
    /** @var array */
    public $free_trial;
    /** @var array */
    public $initial_period;
    /** @var array */
    public $recurring_period;
    
    /**
     * Fill depending on https://developers.bluesnap.com/v8976-Extended/docs/one-time-charge
     * @param decimal $amount
     * @param bool $basePrice
     * @param string $currency
     */
    public function setOneTimeCharge($amount, $basePrice = true, $currency = 'USD')
    {
        $this->one_time_charge['catalog_prices']['catalog_price'] = [
            'amount' => $amount,
            'base_price' => $basePrice,
            'currency' => $currency,
        ];
    }
    
    /**
     * Possible intervals
     */
    const INTERVAL_DAYS = 'DAYS';
    const INTERVAL_MONTHS = 'MONTHS';
    const INTERVAL_YEARS = 'YEARS';
    
    /**
     * Fill depending on https://developers.bluesnap.com/v8976-Extended/docs/free-trial
     * @param int $periodLength
     * @param string $interval
     */
    public function setFreeTrial($periodLength, $interval = self::INTERVAL_DAYS)
    {
        $this->free_trial = [
            'period_length' => $periodLength,
            'interval' => $interval,
        ];
    }
    
    /**
     * Fill depending on https://developers.bluesnap.com/v8976-Extended/docs/initial-period
     * @param int $periodLength
     * @param decimal $amount
     * @param string $interval
     * @param string $currency
     * @param bool $basePrice
     */
    public function setInitialPeriod($periodLength, $amount, $interval = self::INTERVAL_DAYS, $currency = 'USD', $basePrice = true)
    {
        $this->initial_period = [
            'catalog_prices' => [
                'catalog_price' => [
                    'amount' => $amount,
                    'base_price' => $basePrice,
                    'currency' => $currency,
                ]
            ],
            'period_length' => $periodLength,
            'interval' => $interval,
        ];
    }
    
    /**
     * Fill depending on https://developers.bluesnap.com/v8976-Extended/docs/recurring-period
     * @param string $periodFrequency
     * PricingSettings has constants for it
     * @param decimal $amount
     * @param string $interval
     * @param string $currency
     * @param bool $basePrice
     */
    public function setRecurringPeriod($periodFrequency, $amount, $currency = 'USD', $basePrice = true)
    {
        $this->recurring_period = [
            'catalog_prices' => [
                'catalog_price' => [
                    'amount' => $amount,
                    'base_price' => $basePrice,
                    'currency' => $currency,
                ]
            ],
            'period_frequency' => $periodFrequency,
        ];
    }
    
    /**
     * @return array
     */
    public function getActiveType()
    {
        foreach ($this->getData() as $key => $value) {
            if (empty($value['catalog_prices'])) {
                continue;
            }
            return $value;
        }
    }
    
    /**
     * @return array
     */
    public function getCatalogPrice()
    {
        $type = $this->getActiveType();
        return isset($type['catalog_prices']['catalog_price']) ?
            $type['catalog_prices']['catalog_price'] : [];
    }
    
    /**
     * @return numeric
     */
    public function getPrice()
    {
        $catalogPrice = $this->getCatalogPrice();
        return isset($catalogPrice['amount']) ? $catalogPrice['amount'] : 0;
    }
    
    /**
     * @return string
     */
    public function getCurrency()
    {
        $catalogPrice = $this->getCatalogPrice();
        return isset($catalogPrice['currency']) ? $catalogPrice['currency'] : '';
    }
    
    /**
     * @return string
     */
    public function getPeriodFrequency()
    {
        $type = $this->getActiveType();
        return isset($type['period_frequency']) ?
            $type['period_frequency'] : '';
    }
}
