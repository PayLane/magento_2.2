<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface SecureFormConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface SecureFormConfigProviderInterface extends SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isSendCustomerData(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;
}
