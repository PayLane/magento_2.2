<?php

declare(strict_types=1);

/**
 * File: NotificationAuthenticationConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config;

use LizardMedia\PayLane\Api\Config\NotificationAuthenticationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class NotificationAuthenticationConfigProvider
 * @package LizardMedia\PayLane\Model\Config
 */
class NotificationAuthenticationConfigProvider implements NotificationAuthenticationConfigProviderInterface
{
    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_USERNAME = 'payment/paylane/paylane_notifications/username';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_PASSWORD = 'payment/paylane/paylane_notifications/password';

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
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_USERNAME,
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
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_PASSWORD,
            $scopeType,
            $scopeCode
        );
    }
}
