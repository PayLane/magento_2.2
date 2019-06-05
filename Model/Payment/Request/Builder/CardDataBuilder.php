<?php

declare(strict_types=1);

/**
 * File: CardDataBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class CardDataBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class CardDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $payment = $quote->getPayment();
        $result = [
            'card' => [
                'card_number' => $payment->getAdditionalInformation('cc_number'),
                'expiration_month' => sprintf("%02d", $payment->getAdditionalInformation('cc_exp_month')),
                'expiration_year' => $payment->getAdditionalInformation('cc_exp_year'),
                'card_code' => $payment->getAdditionalInformation('cc_cid'),
                'name_on_card' => $payment->getAdditionalInformation('cc_holder_name')
            ]
        ];

        return $result;
    }
}
