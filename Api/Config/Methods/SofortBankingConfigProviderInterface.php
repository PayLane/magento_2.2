<?php

declare(strict_types=1);

/**
 * File: SofortBankingConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

/**
 * Interface SofortBankingConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface SofortBankingConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
