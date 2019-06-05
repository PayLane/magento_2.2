<?php

declare(strict_types=1);

/**
 * File: SofortBankingConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\SofortBankingConfigProviderInterface;

/**
 * Class SofortBankingConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class SofortBankingConfigProvider extends AbstractToggleableMethodConfigProvider implements
    SofortBankingConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_SOFORT = 'paylane_sofort';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_SOFORT;
    }
}