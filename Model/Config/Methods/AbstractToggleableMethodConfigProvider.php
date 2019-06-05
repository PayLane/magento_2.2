<?php

declare(strict_types=1);

/**
 * File: AbstractToggleableMethodConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\ToggleableMethodConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class AbstractToggleableMethodConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
abstract class AbstractToggleableMethodConfigProvider extends AbstractMethodConfigProvider implements
    ToggleableMethodConfigProviderInterface
{
    /**
     * @var string
     */
    const ACTIVE_FIELD = 'active';

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isActive(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::ACTIVE_FIELD),
            $scopeType,
            $scopeCode
        );
    }
}
