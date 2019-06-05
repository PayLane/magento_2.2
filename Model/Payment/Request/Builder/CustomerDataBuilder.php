<?php

declare(strict_types=1);

/**
 * File: CustomerDataBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */

namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class CustomerDataBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $billingAddress = $quote->getBillingAddress();

        $result['customer'] = [
            'name' => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            'email' => $billingAddress->getEmail(),
            'ip' => $quote->getRemoteIp(),
            'address' => [
                'street_house' => implode(',', $billingAddress->getStreet(true)),
                'city' => $billingAddress->getCity(),
                'state' => $billingAddress->getRegion(),
                'zip' => $billingAddress->getPostcode(),
                'country_code' => $billingAddress->getCountryId(),
            ],
        ];

        return $result;
    }
}
