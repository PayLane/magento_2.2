<?php

declare(strict_types=1);

/**
 * File: IdealConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\IdealConfigProviderInterface;

/**
 * Class IdealConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class IdealConfigProvider extends AbstractToggleableMethodConfigProvider implements
    IdealConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_IDEAL = 'paylane_ideal';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_IDEAL;
    }
}
