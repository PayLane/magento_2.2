<?php

declare(strict_types=1);

/**
 * File: AccountBuilder.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */

namespace LizardMedia\PayLane\Model\Payment\Request\Builder;

use LizardMedia\PayLane\Api\Config\Methods\DirectDebitConfigProviderInterface;
use Magento\Quote\Model\Quote;

/**
 * Class AccountBuilder
 * @package LizardMedia\PayLane\Model\Payment\Request\Builder
 */
class AccountBuilder implements BuilderInterface
{
    /**
     * @var DirectDebitConfigProviderInterface
     */
    private $directDebitConfigProvider;

    /**
     * @param DirectDebitConfigProviderInterface $directDebitConfigProvider
     */
    public function __construct(DirectDebitConfigProviderInterface $directDebitConfigProvider)
    {
        $this->directDebitConfigProvider = $directDebitConfigProvider;
    }

    /**
     * @inheritdoc
     */
    public function build(Quote $quote)
    {
        $payment = $quote->getPayment();
        $result = [
            'account' => [
                'account_holder' => $payment->getAdditionalInformation('account_holder'),
                'account_country' => $payment->getAdditionalInformation('account_country'),
                'iban' => $payment->getAdditionalInformation('iban'),
                'bic' => $payment->getAdditionalInformation('bic'),
                'mandate_id' => $this->directDebitConfigProvider->getMandateId()
            ]
        ];

        return $result;
    }
}
