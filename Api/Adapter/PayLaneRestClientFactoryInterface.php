<?php

declare(strict_types=1);

/**
 * File: PayLaneRestClientFactoryInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Adapter;

use LizardMedia\PayLane\Model\Adapter\PayLaneRestClient;

/**
 * Interface PayLaneRestClientFactoryInterface
 * @package LizardMedia\PayLane\Api
 */
interface PayLaneRestClientFactoryInterface
{
    /**
     * @return PayLaneRestClient
     */
    public function create(): PayLaneRestClient;
}
