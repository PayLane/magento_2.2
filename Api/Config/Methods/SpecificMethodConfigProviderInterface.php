<?php

declare(strict_types=1);

/**
 * File: SpecificMethodConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface SpecificMethodConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getTitle(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getSortOrder(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): int;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isPaymentMethodImageShown(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;
}
