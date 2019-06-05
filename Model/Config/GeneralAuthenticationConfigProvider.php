<?php

declare(strict_types=1);

/**
 * File: GeneralAuthenticationConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config;

use LizardMedia\PayLane\Api\Config\GeneralAuthenticationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class GeneralAuthenticationConfigProvider
 * @package LizardMedia\PayLane\Model\Config
 */
class GeneralAuthenticationConfigProvider implements GeneralAuthenticationConfigProviderInterface
{
    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_USERNAME = 'payment/paylane/username';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_PASSWORD = 'payment/paylane/password';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * NotificationAuthenticationConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getUsername(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_USERNAME,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPassword(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_PASSWORD,
            $scopeType,
            $scopeCode
        );
    }
}
