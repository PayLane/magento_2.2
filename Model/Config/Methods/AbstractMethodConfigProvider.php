<?php

declare(strict_types=1);

/**
 * File: AbstractMethodConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\SpecificMethodConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class AbstractMethodConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
abstract class AbstractMethodConfigProvider implements SpecificMethodConfigProviderInterface
{
    /**
     * @var string
     */
    const PAYMENT_CONFIG_PATTERN = 'payment/%s/%s';

    /**
     * @var string
     */
    const TITLE_FIELD = 'title';

    /**
     * @var string
     */
    const SORT_ORDER = 'sort_order';

    /**
     * @var string
     */
    const SHOW_IMG = 'show_img';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AbstractMethodConfigProvider constructor.
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
    public function getTitle(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::TITLE_FIELD),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getSortOrder(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): int {
        return (int) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::SORT_ORDER),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isPaymentMethodImageShown(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::SHOW_IMG),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $fieldPathPart
     * @return string
     */
    protected function buildPathForConfiguration(string $fieldPathPart): string
    {
        return sprintf(self::PAYMENT_CONFIG_PATTERN, $this->getMethodCode(), $fieldPathPart);
    }

    /**
     * @return string
     */
    abstract protected function getMethodCode(): string;
}
