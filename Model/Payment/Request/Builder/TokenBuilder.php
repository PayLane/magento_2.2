<?php

declare(strict_types=1);

/**
 * File: TokenBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class TokenBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class TokenBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $payment = $quote->getPayment();
        $result = [
            'card' => [
                'token' => $payment->getAdditionalInformation('token')
            ]
        ];

        return $result;
    }
}
