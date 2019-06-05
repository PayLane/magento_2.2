<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface CreditCardConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface CreditCardConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isFraudCheckOverwritten(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isFraudCheckEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isAVSCheckOverwritten(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getAVSCheckLevel(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function is3DSCheckEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getBlockedAmountInAuthorizationProcess(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): float;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isSingleClickPaymentEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;
}
