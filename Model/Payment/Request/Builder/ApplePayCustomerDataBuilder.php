<?php

declare(strict_types=1);

/**
 * File: ApplePayCustomerDataBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */

namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class ApplePayCustomerDataBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class ApplePayCustomerDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $billingAddress = $quote->getBillingAddress();

        $result['customer'] = [
            'name' => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            'email' => $billingAddress->getEmail(),
            'ip' => $quote->getRemoteIp(),
            'country_code' => $billingAddress->getCountryId()
        ];

        return $result;
    }
}
