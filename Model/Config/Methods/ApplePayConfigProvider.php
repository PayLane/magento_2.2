<?php

declare(strict_types=1);

/**
 * File: ApplePayConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\ApplePayConfigProviderInterface;

/**
 * @TODO: Unit test
 * Class ApplePayConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class ApplePayConfigProvider extends AbstractToggleableMethodConfigProvider implements ApplePayConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_APPLE_PAY = 'paylane_applepay';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_APPLE_PAY;
    }
}
