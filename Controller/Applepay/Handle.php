<?php

/**
 * File: Handle.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

declare(strict_types=1);

namespace LizardMedia\PayLane\Controller\Applepay;

use LizardMedia\PayLane\Model\Payment\Adapter\Applepay;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Handle
 * @package LizardMedia\PayLane\Controller\Applepay
 */
class Handle extends Action
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Applepay
     */
    protected $adapter;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Handle constructor.
     *
     * @param Context $context
     * @param CartRepositoryInterface $quoteRepository
     * @param Applepay $adapter
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        CartRepositoryInterface $quoteRepository,
        Applepay $adapter,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->adapter = $adapter;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     * @throws \Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute()
    {
        $params = $this->parseRequestParams();
        $quote = $this->quoteRepository->get($params['quote_id']);

        if ($quote->getId()) {
            $this->savePaymentData($quote, $params);

            $params['customer']['ip'] = $_SERVER['REMOTE_ADDR'];

            $this->adapter->setQuote($quote);
            $this->adapter->setParams($params);
            $responseData = $this->adapter->handleRequest();

            $order = $this->adapter->handleResponse($responseData, $this->getResponse());

            $additionalData = [];

            if (is_string($order)) {
                return $this->jsonFactory->create()->setData([
                    'message' => [
                        'result' => $order
                    ],
                    'error_description' => 'no checkout method',
                    'success' => false
                ]);
            }

            if (is_string($order) || !$order->getId()) {
                if (isset($responseData['error']) && isset($responseData['error']['error_description'])) {
                    $errorDescription = $responseData['error']['error_description'];
                } else {
                    $errorDescription = __(
                        'Error while processing order - please contact store administrator or try again'
                    );
                }
            } else {
                $errorDescription = null;
                $additionalData = [
                    'quote_id' => $quote->getId(),
                    'order_id' => $order->getId(),
                    'order_status' => $order->getStatus(),
                    'increment_id' => $order->getIncrementId()
                ];
            }

            return $this->jsonFactory->create()->setData(array_merge([
                'success' => $order->getId() ? true : false,
                'error_description' => $errorDescription
            ], $additionalData));
        } else {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'error_description' => __('Quote with provided ID doesn\'t exists')
            ]);
        }
    }

    /**
     * @return mixed
     */
    private function parseRequestParams()
    {
        $params = json_decode(file_get_contents('php://input'), true);
        return $params;
    }

    /**
     * @param CartInterface $quote
     * @param $params
     * @return void
     */
    private function savePaymentData(CartInterface $quote, $params)
    {
        $payment = $quote->getPayment();
        $payment->setMethod('paylane_applepay');
        $payment->setAdditionalInformation('token', $params['card']['token']);
        $payment->save();

        $quote->reserveOrderId();

        if (!$quote->getCheckoutMethod()) {
            if (!$quote->getCustomer()->getEmail()) {
                $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
            } else {
                $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
            }
        }

        $quote->save();
    }
}
