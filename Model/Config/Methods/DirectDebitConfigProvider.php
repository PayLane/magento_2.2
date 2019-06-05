<?php

declare(strict_types=1);

/**
 * File: DirectDebitConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\DirectDebitConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class DirectDebitConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class DirectDebitConfigProvider extends AbstractToggleableMethodConfigProvider implements
    DirectDebitConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_DIRECT_DEBIT = 'paylane_directdebit';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_DIRECTDEBIT_MANDATE_ID = 'payment/paylane_directdebit/mandate_id';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_DIRECT_DEBIT;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMandateId(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_DIRECTDEBIT_MANDATE_ID,
            $scopeType,
            $scopeCode
        );
    }
}
