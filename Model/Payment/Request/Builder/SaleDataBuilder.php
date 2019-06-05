<?php

declare(strict_types=1);

/**
 * File: SaleDataBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class SaleDataBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class SaleDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $result = [
            'sale' => [
                'amount' => sprintf('%01.2f', $quote->getGrandTotal()),
                'currency' => $quote->getBaseCurrencyCode(),
                'description' => $quote->getReservedOrderId()
            ]
        ];

        return $result;
    }
}
