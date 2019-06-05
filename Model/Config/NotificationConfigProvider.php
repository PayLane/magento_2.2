<?php

declare(strict_types=1);

/**
 * File: NotificationConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config;

use LizardMedia\PayLane\Api\Config\NotificationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class NotificationConfigProvider
 * @package LizardMedia\PayLane\Model\Config
 */
class NotificationConfigProvider implements NotificationConfigProviderInterface
{
    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_URL = 'payment/paylane/paylane_notifications/notifications_url';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_MODE = 'payment/paylane/paylane_notifications/mode';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_TOKEN = 'payment/paylane/paylane_notifications/token';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_ENABLE_LOG = 'payment/paylane/paylane_notifications/enable_log';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * GeneralConfigProvider constructor.
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
    public function getAutomaticNotificationsHandlingUrl(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_URL,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationHandlingMode(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_MODE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationToken(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_TOKEN,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_ENABLE_LOG,
            $scopeType,
            $scopeCode
        );
    }
}
