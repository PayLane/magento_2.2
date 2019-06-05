<?php

declare(strict_types=1);

/**
 * File: ToggleableMethodConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface ToggleableMethodConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface ToggleableMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isActive(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;
}
