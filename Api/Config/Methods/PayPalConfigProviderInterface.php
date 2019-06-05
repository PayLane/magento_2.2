<?php

declare(strict_types=1);

/**
 * File: PayPalConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

/**
 * Interface PayPalConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface PayPalConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
