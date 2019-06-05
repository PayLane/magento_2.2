<?php

declare(strict_types=1);

/**
 * File: DirectDebitConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface DirectDebitConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface DirectDebitConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMandateId(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;
}
