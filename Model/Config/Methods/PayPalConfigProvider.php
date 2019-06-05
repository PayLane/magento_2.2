<?php

declare(strict_types=1);

/**
 * File: PayPalConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\PayPalConfigProviderInterface;

/**
 * Class PayPalConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class PayPalConfigProvider extends AbstractToggleableMethodConfigProvider implements
    PayPalConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_PAYPAL = 'paylane_paypal';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_PAYPAL;
    }
}
