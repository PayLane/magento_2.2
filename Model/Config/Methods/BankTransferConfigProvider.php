<?php

declare(strict_types=1);

/**
 * File: BankTransferConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\BankTransferConfigProviderInterface;

/**
 * @TODO: Unit test
 * Class BankTransferConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class BankTransferConfigProvider extends AbstractToggleableMethodConfigProvider implements
    BankTransferConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_BANK_TRANSFER = 'paylane_banktransfer';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_BANK_TRANSFER;
    }
}
