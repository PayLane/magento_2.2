<?php

declare(strict_types=1);

/**
 * File: BankCodeBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class BankCodeBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class BankCodeBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $payment = $quote->getPayment();
        $result = [
            'bank_code' => $payment->getAdditionalInformation('bank_code')
        ];

        return $result;
    }
}
