<?php

declare(strict_types=1);

/**
 * File: IdealConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

/**
 * Interface IdealConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface IdealConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
