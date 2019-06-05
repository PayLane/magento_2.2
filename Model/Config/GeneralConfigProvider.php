<?php

declare(strict_types=1);

/**
 * File: GeneralConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config;

use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Model\Config\Methods\BankTransferConfigProvider;
use LizardMedia\PayLane\Model\Config\Methods\CreditCardConfigProvider;
use LizardMedia\PayLane\Model\Config\Methods\DirectDebitConfigProvider;
use LizardMedia\PayLane\Model\Config\Methods\IdealConfigProvider;
use LizardMedia\PayLane\Model\Config\Methods\PayPalConfigProvider;
use LizardMedia\PayLane\Model\Config\Methods\SecureFormConfigProvider;
use LizardMedia\PayLane\Model\Config\Methods\SofortBankingConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class GeneralConfigProvider
 * @package LizardMedia\PayLane\Model\Config
 */
class GeneralConfigProvider implements GeneralConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_PAYLANE = 'paylane';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_ENABLE = 'payment/paylane/enable';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_TITLE = 'payment/paylane/title';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_SORT_ORDER = 'payment/paylane/sort_order';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_PAYMENT_MODE = 'payment/paylane/payment_mode';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_HASH_SALT = 'payment/paylane/hash_salt';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_MERCHANT_ID = 'payment/paylane/merchant_id';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_API_KEY = 'payment/paylane/api_key';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_PENDING_ORDER_STATUS = 'payment/paylane/pending_order_status';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_PERFORMED_ORDER_STATUS = 'payment/paylane/performed_order_status';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_CLEARED_ORDER_STATUS = 'payment/paylane/cleared_order_status';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_ERROR_ORDER_STATUS = 'payment/paylane/error_order_status';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_REDIRECT_VERSION = 'payment/paylane/redirect_version';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_ENABLE_LOG = 'payment/paylane/enable_log';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * GeneralConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(self::XML_PATH_PAYMENT_PAYLANE_ENABLE, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getTitle(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_TITLE, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getSortOrder(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): int {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_SORT_ORDER, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPaymentMode(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_PAYMENT_MODE, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getHashSalt(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_HASH_SALT, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMerchantId(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_MERCHANT_ID, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getApiKey(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_API_KEY, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPendingOrderStatus(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_PENDING_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPerformedOrderStatus(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_PERFORMED_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getClearedOrderStatus(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_CLEARED_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getErrorOrderStatus(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_ERROR_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getRedirectVersion(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_REDIRECT_VERSION,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param string $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(self::XML_PATH_PAYMENT_PAYLANE_ENABLE_LOG, $scopeType, $scopeCode);
    }

    /**
     * @return array
     */
    public function getPaymentCodes(): array
    {
        return [
            BankTransferConfigProvider::CODE_BANK_TRANSFER,
            CreditCardConfigProvider::CODE_CREDIT_CARD,
            DirectDebitConfigProvider::CODE_DIRECT_DEBIT,
            IdealConfigProvider::CODE_IDEAL,
            PayPalConfigProvider::CODE_PAYPAL,
            SecureFormConfigProvider::CODE_SECURE_FORM,
            SofortBankingConfigProvider::CODE_SOFORT
        ];
    }
}
