<?php

namespace achertovsky\bluesnap\models;

use achertovsky\bluesnap\models\ChargePolicy;
use achertovsky\bluesnap\traits\Common;
use yii\base\Model;

/**
 * @author alexander
 */
class PricingSettings extends Model
{
    use Common;
    /**
     * Possible policy types
     */
    const POLICY_TYPE_ONE_TIME_PAYMENT = 'ONE TIME PAYMENT';
    const POLICY_TYPE_ONE_TIME_PAYMENT_WITH_TRIAL = 'ONE TIME PAYMENT WITH TRIAL';
    const POLICY_TYPE_STANDARD_SUBSCRIPTION = 'STANDARD SUBSCRIPTION';
    const POLICY_TYPE_STANDARD_SUBSCRIPTION_WITH_TRIAL = 'STANDARD SUBSCRIPTION WITH TRIAL';
    const POLICY_TYPE_STANDARD_SUBSCRIPTION_WITH_INITIAL_CHARGE = 'STANDARD SUBSCRIPTION WITH INITIAL CHARGE';
    
    /**
     * Possible rounding values
     */
    const ROUNDING_DISABLE = "NO ROUNDING";
    const ROUNDING_UP_TO_5_CENTS = "ROUNDING UP TO 0.05";
    const ROUNDING_UP_TO_10_CENTS = "ROUNDING UP TO 0.10";
    const ROUNDING_UP_TO_50_CENTS = "ROUNDING UP TO 0.50";
    const ROUNDING_UP_TO_95_CENTS = "ROUNDING UP TO 0.95";
    const ROUNDING_UP_TO_99_CENTS = "ROUNDING UP TO 0.99";
    const ROUNDING_UP_TO_1_DOLLAR = "ROUNDING UP TO 1.00";
    const ROUNDING_UP_TO_5_DOLLAR = "ROUNDING UP TO 5.00";
    
    /**
     * Possible period frequencies
     */
    const PERIOD_FREQUENCY_NONE = "NONE";
    const PERIOD_FREQUENCY_ONCE = "ONCE";
    const PERIOD_FREQUENCY_DAILY = "DAILY";
    const PERIOD_FREQUENCY_WEEKLY = "WEEKLY";
    const PERIOD_FREQUENCY_BIWEEKLY = "BIWEEKLY";
    const PERIOD_FREQUENCY_MONTHLY = "MONTHLY";
    const PERIOD_FREQUENCY_BIMONTHLY = "BIMONTHLY";
    const PERIOD_FREQUENCY_QUARTERLY = "QUARTERLY";
    const PERIOD_FREQUENCY_SEMIANNUALLY = "SEMIANNUALLY";
    const PERIOD_FREQUENCY_ANNUALLY = "ANNUALLY";
    const PERIOD_FREQUENCY_BIANNUALLY = "BIANNUALLY";
    const PERIOD_FREQUENCY_TRIANNUALLY = "TRIANNUALLY";
    const PERIOD_FREQUENCY_UPON_DEMAND = "UPON_DEMAND";
    
    /** @var string */
    public $charge_policy_type;
    /** @var array */
    public $charge_policy;
    /** @var bool */
    public $include_tax_in_price = false;
    /** @var string */
    public $rounding_price_method = self::ROUNDING_DISABLE;
    /** @var array */
    public $recurring_plan_settings;
    
    /**
     * @param bool $chargeUponPlanChange
     * @param int $gracePeriodLength
     * @param decimal $planChargeAmountLimit
     * @param string $currency
     * @param int $planDurationPeriod
     * @param int $planMaxChargeNumberLimit
     */
    public function setRecurringPlanSettings(
        $chargeUponPlanChange = true,
        $gracePeriodLength = 0,
        $planChargeAmountLimit = null,
        $currency = 'USD',
        $planDurationPeriod = null,
        $planMaxChargeNumberLimit = null
    )
    {
        $this->recurring_plan_settings = [
            'charge_upon_plan_change' => $chargeUponPlanChange,
            'grace_period_length' => $gracePeriodLength,
        ];
        if (!is_null($planChargeAmountLimit)) {
            $this->recurring_plan_settings['plan_charge_amount_limit'] = [
                'currency' => $currency,
                'amount' => $planChargeAmountLimit,
            ];
        }
        if (!is_null($planDurationPeriod)) {
            $this->recurring_plan_settings['plan_duration_period'] = $planDurationPeriod;
        }
        if (!is_null($planMaxChargeNumberLimit)) {
            $this->recurring_plan_settings['plan_max_charge_number_limit'] = $planMaxChargeNumberLimit;
        }
    }
    
    /**
     * @param decimal $amount
     * @param string $currency
     * @param bool $basePrice
     * @return array
     */
    public function getOneTimePayment($amount, $currency = 'USD', $basePrice = true)
    {
        $this->charge_policy_type = self::POLICY_TYPE_ONE_TIME_PAYMENT;
        $chargePolicy = new ChargePolicy();
        $chargePolicy->setOneTimeCharge($amount, $basePrice, $currency);
        $this->charge_policy = $chargePolicy->getData();
        return $this->getData();
    }
    
    /**
     * @param decimal $amount
     * @param int $trialLenght
     * @param string $currency
     * @param string $trialInterval
     * @param bool $basePrice
     * @return array
     */
    public function getOneTimePaymentWithTrial($amount, $trialLenght, $currency = 'USD', $trialInterval = ChargePolicy::INTERVAL_DAYS, $basePrice = true)
    {
        $this->charge_policy_type = self::POLICY_TYPE_ONE_TIME_PAYMENT_WITH_TRIAL;
        $chargePolicy = new ChargePolicy();
        $chargePolicy->setFreeTrial($trialLenght, $trialInterval);
        $chargePolicy->setOneTimeCharge($amount, $basePrice, $currency);
        $this->charge_policy = $chargePolicy->getData();
        return $this->getData();
    }
    
    /**
     * @param decimal $amount
     * @param string $periodFrequency
     * PricingSettings has constants for it
     * @param string $currency
     * @param bool $basePrice
     * @return array
     */
    public function getSubscription($amount, $periodFrequency, $currency = 'USD', $basePrice = true)
    {
        $this->charge_policy_type = self::POLICY_TYPE_STANDARD_SUBSCRIPTION;
        $chargePolicy = new ChargePolicy();
        $chargePolicy->setRecurringPeriod($periodFrequency, $amount, $currency, $basePrice);
        $this->setRecurringPlanSettings();
        $this->charge_policy = $chargePolicy->getData();
        return $this->getData();
    }
    
    /**
     * @param decimal $amount
     * @param string $periodFrequency
     * PricingSettings has constants for it
     * @param int $trialLenght
     * @param string $currency
     * @param string $trialInterval
     * @param bool $basePrice
     * @return array
     */
    public function getSubscriptionWithTrial($amount, $periodFrequency, $trialLenght, $currency = 'USD', $trialInterval = ChargePolicy::INTERVAL_DAYS, $basePrice = true)
    {
        $this->charge_policy_type = self::POLICY_TYPE_STANDARD_SUBSCRIPTION_WITH_TRIAL;
        $chargePolicy = new ChargePolicy();
        $chargePolicy->setFreeTrial($trialLenght, $trialInterval);
        $chargePolicy->setRecurringPeriod($periodFrequency, $amount, $currency, $basePrice);
        $this->setRecurringPlanSettings();
        $this->charge_policy = $chargePolicy->getData();
        return $this->getData();
    }
    
    /**
     * @param decimal $amount
     * @param string $periodFrequency
     * PricingSettings has constants for it
     * @param int $initialPeriod
     * @param decimal $initialAmount
     * @param string $currency
     * @param string $initialInterval
     * @param bool $basePrice
     * @return array
     */
    public function getSubscriptionWithInitialCharge($amount, $periodFrequency, $initialPeriod, $initialAmount, $currency = 'USD', $initialInterval = ChargePolicy::INTERVAL_DAYS, $basePrice = true)
    {
        $this->charge_policy_type = self::POLICY_TYPE_STANDARD_SUBSCRIPTION_WITH_INITIAL_CHARGE;
        $chargePolicy = new ChargePolicy();
        $chargePolicy->setInitialPeriod($initialPeriod, $initialAmount, $initialInterval, $currency, $basePrice);
        $chargePolicy->setRecurringPeriod($periodFrequency, $amount, $currency, $basePrice);
        $this->setRecurringPlanSettings();
        $this->charge_policy = $chargePolicy->getData();
        return $this->getData();
    }
    
    /**
     * @return numeric
     */
    public function getPrice()
    {
        $chargePolicy = new ChargePolicy($this->charge_policy);
        return $chargePolicy->getPrice();
    }
    
    /**
     * @return string
     */
    public function getCurrency()
    {
        $chargePolicy = new ChargePolicy($this->charge_policy);
        return $chargePolicy->getCurrency();
    }
    
    /**
     * @return string
     */
    public function getPeriodFrequency()
    {
        $chargePolicy = new ChargePolicy($this->charge_policy);
        return $chargePolicy->getPeriodFrequency();
    }
    
    /**
     * @param string $type
     * @return boolean
     */
    public static function isSubscription($type)
    {
        if (in_array(
                $type,
                [
                    self::POLICY_TYPE_STANDARD_SUBSCRIPTION,
                    self::POLICY_TYPE_STANDARD_SUBSCRIPTION_WITH_INITIAL_CHARGE,
                    self::POLICY_TYPE_STANDARD_SUBSCRIPTION_WITH_TRIAL
                ]
            )
        ) {
            return true;
        }
        return false;
    }
}
