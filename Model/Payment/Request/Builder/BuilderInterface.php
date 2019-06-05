<?php

declare(strict_types=1);

/**
 * File: BuilderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Interface BuilderInterface
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
interface BuilderInterface
{
    /**
     * @param Quote $quote
     * @return mixed
     */
    public function build(Quote $quote);
}
