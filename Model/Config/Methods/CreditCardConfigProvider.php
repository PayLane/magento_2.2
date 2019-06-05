<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\CreditCardConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CreditCardConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class CreditCardConfigProvider extends AbstractToggleableMethodConfigProvider implements
    CreditCardConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_CREDIT_CARD = 'paylane_creditcard';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_OVERWRITE = 'payment/paylane_creditcard/fraud_check_overwrite';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_ENABLED = 'payment/paylane_creditcard/fraud_check';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_OVERWRITE = 'payment/paylane_creditcard/avs_check_overwrite';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_LEVEL = 'payment/paylane_creditcard/avs_check_level';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AUTHORIZATION_AMOUNT = 'payment/paylane_creditcard/authorization_amount';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_SINGLE_CLICK_ACTIVE = 'payment/paylane_creditcard/single_click_active';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_DS3_CHECK = 'payment/paylane_creditcard/ds3_check';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_CREDIT_CARD;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isFraudCheckOverwritten(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_OVERWRITE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isFraudCheckEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_ENABLED,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isAVSCheckOverwritten(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_OVERWRITE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getAVSCheckLevel(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_LEVEL,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function is3DSCheckEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_DS3_CHECK,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return float
     */
    public function getBlockedAmountInAuthorizationProcess(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): float {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AUTHORIZATION_AMOUNT,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isSingleClickPaymentEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_SINGLE_CLICK_ACTIVE,
            $scopeType,
            $scopeCode
        );
    }
}
