<?php

declare(strict_types=1);

/**
 * File: ApplePayConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

/**
 * Interface ApplePayConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface ApplePayConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
