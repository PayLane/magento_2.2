<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Config\Methods;

use LizardMedia\PayLane\Api\Config\Methods\SecureFormConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class SecureFormConfigProvider
 * @package LizardMedia\PayLane\Model\Config\Methods
 */
class SecureFormConfigProvider extends AbstractMethodConfigProvider implements SecureFormConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE_SECURE_FORM = 'paylane_secureform';

    /**
     * @var string
     */
    const XML_PATH_PAYMENT_PAYLANE_SECUREFORM_SEND_CUSTOMER_DATA = 'payment/paylane_secureform/send_customer_data';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_SECURE_FORM;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isSendCustomerData(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_SECUREFORM_SEND_CUSTOMER_DATA,
            $scopeType,
            $scopeCode
        );
    }
}
