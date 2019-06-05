<?php

declare(strict_types=1);

/**
 * File: Start.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\Transaction;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Start
 * @package LizardMedia\PayLane\Controller\Transaction
 */
class Start extends Action
{
    /**
     * @var Session
     */
    protected $session;
    
    /**
     * Start constructor.
     * @param Context $context
     * @param Session $session
     * @param StoreInterface $store
     */
    public function __construct(
        Context $context,
        Session $session
    ) {
        parent::__construct($context);
        $this->session = $session;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     * @throws LocalizedException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $paymentMethodCode = $params['method_code'];

        if ($paymentMethodCode) {
            $quote = $this->session->getQuote();

            if (!$quote->getCheckoutMethod()) {
                if (!$quote->getCustomer()->getEmail()) {
                    $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
                } else {
                    $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
                }
            }

            if ($quote->getId()) {
                $payment = $quote->getPayment();
                $payment->setMethod($paymentMethodCode);

                $objectManager = ObjectManager::getInstance();
                $adapter = $objectManager->create('LizardMedia\PayLane\Model\Payment\Adapter\\' . ucwords(str_replace('paylane_', '', $paymentMethodCode)));

                $additionalFields = $adapter->getAdditionalFields();

                foreach ($additionalFields as $field) {
                    if (isset($params['additional_data'][$field])) {
                        $payment->setAdditionalInformation($field, $params['additional_data'][$field]);
                    }
                }

                $payment->save();

                $quote->reserveOrderId();
                $quote->save();

                $adapter->setQuote($quote);
                $responseData = $adapter->handleRequest();

                $adapter->handleResponse($responseData, $this->getResponse());
            } else {
                $this->_redirect('checkout/onepage/failure', ['_nosid' => true, '_secure' => true]);
            }
        } else {
            $this->_redirect('checkout/onepage/failure', ['_nosid' => true, '_secure' => true]);
        }
    }
}
