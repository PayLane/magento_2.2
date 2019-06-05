<?php

declare(strict_types=1);

/**
 * File: NotificationConfigProviderInterface.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Api\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface NotificationConfigProviderInterface
 * @package LizardMedia\PayLane\Api\Config
 */
interface NotificationConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getAutomaticNotificationsHandlingUrl(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationHandlingMode(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationToken(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool;
}
