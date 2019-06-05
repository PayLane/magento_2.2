<?php

declare(strict_types=1);

/**
 * File: BankTransferConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config\Methods;

/**
 * Interface BankTransferConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config\Methods
 */
interface BankTransferConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
