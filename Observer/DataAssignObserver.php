<?php

declare(strict_types=1);

/**
 * File: DataAssignObserver.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 * @package LizardMedia\PayLane\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var array
     */
    protected $additionalInformationList = [
        'cc_cid',
        'cc_ss_start_month',
        'cc_ss_start_year',
        'cc_ss_issue',
        'cc_type',
        'cc_exp_year',
        'cc_exp_month',
        'cc_number',
        'cc_holder_name'
    ];

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
