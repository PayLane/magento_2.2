<?php

declare(strict_types=1);

/**
 * File: PayLaneRestClientFactory.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Adapter;

use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use LizardMedia\PayLane\Api\Config\GeneralAuthenticationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * @TODO: Unit test
 * Class PayLaneRestClientFactory
 * @package LizardMedia\PayLane\Model\Adapter
 */
class PayLaneRestClientFactory implements PayLaneRestClientFactoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $generalAuthenticationConfigProvider;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * PayLaneRestClientFactory constructor.
     * @param GeneralAuthenticationConfigProviderInterface $generalAuthenticationConfigProvider
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        GeneralAuthenticationConfigProviderInterface $generalAuthenticationConfigProvider,
        ObjectManagerInterface $objectManager
    ) {
        $this->generalAuthenticationConfigProvider = $generalAuthenticationConfigProvider;
        $this->objectManager = $objectManager;
    }

    /**
     * @return PayLaneRestClient
     */
    public function create(): PayLaneRestClient
    {
        return $this->objectManager->create(
            PayLaneRestClient::class,
            [
                'username' => $this->generalAuthenticationConfigProvider->getUsername(),
                'password' => $this->generalAuthenticationConfigProvider->getPassword()
            ]
        );
    }
}
