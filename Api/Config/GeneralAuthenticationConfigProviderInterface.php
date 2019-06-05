<?php

declare(strict_types=1);

/**
 * File: GeneralAuthenticationConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface GeneralAuthenticationConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config
 */
interface GeneralAuthenticationConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getUsername(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPassword(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;
}
