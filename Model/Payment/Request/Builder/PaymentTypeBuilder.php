<?php

declare(strict_types=1);

/**
 * File: PaymentTypeBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class PaymentTypeBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class PaymentTypeBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $payment = $quote->getPayment();
        $result = [
            'payment_type' => $payment->getAdditionalInformation('payment_type')
        ];

        return $result;
    }
}
